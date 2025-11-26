<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class MovieCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Movie::class;
    }
}
