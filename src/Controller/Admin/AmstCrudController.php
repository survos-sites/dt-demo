<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class AmstCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Amst::class;
    }
}
