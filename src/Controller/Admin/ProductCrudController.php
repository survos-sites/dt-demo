<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class ProductCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Product::class;
    }
}
