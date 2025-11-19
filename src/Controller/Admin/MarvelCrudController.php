<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class MarvelCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Marvel::class;
    }
}
