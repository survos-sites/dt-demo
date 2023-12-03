<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/simple', name: 'app_simple')]
    public function simple(): Response
    {
        return $this->render('app/simple.html.twig', [
            'controllerClass' => self::class
        ]);
    }

    #[Route('/grid', name: 'app_grid')]
    public function grid_example(): Response
    {
        return $this->render('app/grid.html.twig', [
            'controllerClass' => self::class
        ]);
    }

}
