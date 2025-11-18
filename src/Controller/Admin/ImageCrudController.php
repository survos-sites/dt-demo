<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class ImageCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Image::class;
    }
}
