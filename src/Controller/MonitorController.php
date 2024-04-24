<?php

namespace App\Controller;

use Liip\Monitor\Controller\OhDearController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Liip\Monitor\Controller\OhDearController as BaseOhDearController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Twig\Environment;

#[AsController]
#[Route('/health-check')]
// https://github.com/liip/LiipMonitorBundle/tree/3.x?tab=readme-ov-file#ohdear-application-monitoring
class MonitorController extends BaseOhDearController
{
}
