<?php

namespace App\Workflow;

use Survos\WorkflowBundle\Attribute\Place;
use Survos\WorkflowBundle\Attribute\Transition;

// See events at https://symfony.com/doc/current/workflow.html#using-events

interface OfficialWorkflowInterface
{
    // This name is used for injecting the workflow into a service
    // #[Target(OfficialWorkflowInterface::WORKFLOW_NAME)] private WorkflowInterface $workflow
    public const WORKFLOW_NAME = 'OfficialWorkflow';

    #[Place(info: 'loaded from JSON')]
    public const PLACE_NEW = 'new';
    #[Place(info: 'enhanced with wikidata')]
    public const PLACE_DETAILS = 'details';
    #[Place(info: 'resize requested(via sais)')]
    public const string PLACE_RESIZED = 'images_resized';

    #[Transition([self::PLACE_NEW], self::PLACE_DETAILS, transport: 'official_fetch_wiki',
        info: "scrape wiki data",
        next: [self::TRANSITION_RESIZE])]
    public const TRANSITION_FETCH_WIKI = 'fetch_wiki';

    #[Transition([self::PLACE_DETAILS], self::PLACE_RESIZED, info: "dispatch resize to sais")]
    public const string TRANSITION_RESIZE = 'resize';

}
