<?php

namespace App\Workflow;

use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Survos\WikiBundle\Service\WikiService;
use Survos\WorkflowBundle\Attribute\Workflow;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;


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
    public function onTransition(TransitionEvent $event, ): void
    {
        $official = $this->getOfficial($event);
        $filesystem = $this->defaultStorage;
        $this->wikiService->setCacheTimeout(60 * 60 * 24);
        $wikiData = $this->wikiService->fetchWikidataPage($official->getWikidataId());
        $official->setWikiData($wikiData);
        $this->entityManager->flush();

    }

}
