<?php

namespace App\Workflow;

use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\Command\AppLoadDataCommand;
use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemOperator;
use Survos\SaisBundle\Model\ProcessPayload;
use Survos\SaisBundle\Service\SaisClientService;
use Survos\WikiBundle\Service\WikidataService;
use Survos\WikiBundle\Service\WikiService;
use Survos\StateBundle\Attribute\Workflow;
use Survos\StateBundle\Message\TransitionMessage;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\WorkflowInterface;
use Wikidata\Value;
use App\Workflow\OfficialWorkflowInterface as WF;


// See events at https://symfony.com/doc/current/workflow.html#using-events

final class OfficialWorkflow
{

    public function __construct(
        private readonly WikidataService           $wikiService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly SaisClientService     $saisClientService,
    )
    {
    }

    private function getOfficial(Event $event): Official
    {
        /** @var Official */ return $event->getSubject();
    }

    #[AsGuardListener(WF::WORKFLOW_NAME, WF::TRANSITION_FETCH_WIKI)]
    public function onGuard(GuardEvent $event): void
    {
        // @todo: move to guard: in workflow
        if (!$this->getOfficial($event)->getWikidataId()) {
            $event->setBlocked(true, "missing wiki id.");
        }
    }

    #[AsTransitionListener(WF::WORKFLOW_NAME, WF::TRANSITION_FETCH_WIKI)]
    public function onFetchWiki(TransitionEvent $event): void
    {
        $official = $this->getOfficial($event);
//        $this->wikiService->setCacheTimeout(60 * 60 * 24);
        $wikiData = $this->wikiService->get($official->getWikidataId());
        $official->setWikiData($wikiData);
    }

    #[AsTransitionListener(WF::WORKFLOW_NAME, WF::TRANSITION_RESIZE)]
    public function onResize(TransitionEvent $event): void
    {
        $official = $this->getOfficial($event);
        $wikiData = $official->getWikiData();

//        $p18 = $wikiData['properties']['P18'];
        /** @var Collection $values */

        $values = $wikiData['properties']['P18']['values']??[];
//        dump($p18, $values->getIterator());
        /** @var Value $item */
        $images = [];
        foreach ($values as $item) {
            // we could do this in an async message, too.
            if ($url = $item['id']) {
                $official->setOriginalImageUrl($url);
                $response = $this->saisClientService->dispatchProcess(new ProcessPayload(AppLoadDataCommand::SAIS_CLIENT, [
                    $url
                ],
                thumbCallbackUrl: $x=$this->urlGenerator->generate('app_webhook', ['id' => $official->getId()], $this->urlGenerator::ABS_URL)
                ));
                break; // first one only, for now.
            }
        }
    }

}
