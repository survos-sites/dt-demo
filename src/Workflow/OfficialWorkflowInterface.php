<?php

namespace App\Workflow;

use Survos\WorkflowBundle\Attribute\Transition;

// See events at https://symfony.com/doc/current/workflow.html#using-events

interface OfficialWorkflowInterface
{
    // This name is used for injecting the workflow into a service
    // #[Target(OfficialWorkflowInterface::WORKFLOW_NAME)] private WorkflowInterface $workflow
    public const WORKFLOW_NAME = 'OfficialWorkflow';

    public const PLACE_NEW = 'new';
    public const PLACE_DETAILS = 'details';
    public const string PLACE_RESIZED = 'images_resized';

    #[Transition([self::PLACE_NEW], self::PLACE_DETAILS, transport: 'official_fetch_wiki', info: "scrape wiki data",next: [self::TRANSITION_RESIZE])]
    public const TRANSITION_FETCH_WIKI = 'fetch_wiki';

    #[Transition([self::PLACE_DETAILS], self::PLACE_RESIZED, info: "dispatch resize request")]
    public const TRANSITION_RESIZE = 'resize';

}
