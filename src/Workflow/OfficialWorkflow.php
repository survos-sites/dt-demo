<?php

namespace App\Workflow;

use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemOperator;
use Survos\WikiBundle\Service\WikiService;
use Survos\WorkflowBundle\Attribute\Workflow;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Wikidata\Value;


// See events at https://symfony.com/doc/current/workflow.html#using-events

#[Workflow(supports: [Official::class], name: self::WORKFLOW_NAME)]
final class OfficialWorkflow implements OfficialWorkflowInterface
{

    public function __construct(
        private WikiService $wikiService,
        private FilesystemOperator                         $defaultStorage,
        private EntityManagerInterface                      $entityManager,
        // add services
    )
    {
    }

    private function getOfficial(GuardEvent|TransitionEvent $event): Official
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
    public function onFetchWiki(TransitionEvent $event, ): void
    {
        $official = $this->getOfficial($event);
        $filesystem = $this->defaultStorage;
        $this->wikiService->setCacheTimeout(60 * 60 * 24);
        $wikiData = $this->wikiService->fetchWikidataPage($official->getWikidataId());
        $official->setWikiData($wikiData->toArray());

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
                break; // first one only, for now.
            }
        }
    }

}
