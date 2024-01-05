<?php

namespace App\Controller;

use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FlysystemController extends AbstractController
{
    #[Route('/flysystem', name: 'flysystem_browse')]
    public function index(FilesystemOperator $defaultStorage): Response
    {

        return $this->render('flysystem/index.html.twig', [
            'images' => $defaultStorage->listContents('/', deep: true),
            'controller_name' => 'FlysystemController',
        ]);
    }


}
