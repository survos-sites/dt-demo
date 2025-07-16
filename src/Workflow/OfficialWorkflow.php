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
use Survos\WikiBundle\Service\WikiService;
use Survos\WorkflowBundle\Attribute\Workflow;
use Survos\WorkflowBundle\Message\AsyncTransitionMessage;
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


// See events at https://symfony.com/doc/current/workflow.html#using-events

#[Workflow(supports: [Official::class], name: self::WORKFLOW_NAME)]
final class OfficialWorkflow implements OfficialWorkflowInterface
{

    public function __construct(
        private WikiService                              $wikiService,
        private FilesystemOperator                       $defaultStorage,
        private UrlGeneratorInterface                    $urlGenerator,
        private MessageBusInterface                      $messageBus,
        #[Target(self::WORKFLOW_NAME)] private WorkflowInterface $officialWorkflow,
//        private SaisClientService                        $aisClientService,
        private EntityManagerInterface                   $entityManager,
        private readonly SaisClientService               $saisClientService,
        // add services
    )
    {
    }

    private function getOfficial(Event $event): Official
    {
        /** @var Official */ return $event->getSubject();
    }

    #[AsGuardListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
    public function onGuard(GuardEvent $event): void
    {
        if (!$this->getOfficial($event)->getWikidataId()) {
            $event->setBlocked(true, "missing wiki id.");
        }
    }

    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
    public function onFetchWiki(TransitionEvent $event): void
    {
        $official = $this->getOfficial($event);
        $filesystem = $this->defaultStorage;
        $this->wikiService->setCacheTimeout(60 * 60 * 24);
        $wikiData = $this->wikiService->fetchWikidataPage($official->getWikidataId());
        $official->setWikiData($wikiData->toArray());
    }

    #[AsCompletedListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
    public function onFetchWikiCompleted(CompletedEvent $event): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
        // this is because Properties is an Illuminate collection and needs to be an array
        $official = $this->entityManager->find(Official::class, $this->getOfficial($event)->getId());
        foreach ([self::TRANSITION_RESIZE] as $nextTransition) {
            if ($this->officialWorkflow->can($official, $nextTransition)) {
                $env = $this->messageBus->dispatch(new AsyncTransitionMessage(
                    $official->getId(),
                    $official::class,
                    $nextTransition,
                    self::WORKFLOW_NAME,
                ));
            }
        }
    }


    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_RESIZE)]
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
