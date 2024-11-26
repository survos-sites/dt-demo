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

    #[Transition([self::PLACE_NEW], self::PLACE_DETAILS)]
    public const TRANSITION_FETCH_WIKI = 'fetch_wiki';
}
