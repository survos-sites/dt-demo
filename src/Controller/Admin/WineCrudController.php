<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class WineCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Wine::class;
    }
}
