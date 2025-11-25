<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class CarCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Car::class;
    }
}
