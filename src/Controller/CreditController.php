<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreditController extends AbstractController
{
    #[Route('/credit', name: 'app_credit')]
    public function index(ParameterBagInterface $bag): Response
    {
        $map = require($bag->get('kernel.project_dir') . '/importmap.php');
        $data = [];
        foreach ($map as $package => $packageData) {
            if ($version = $packageData['version']??false) {
                // https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js
                // https://www.jsdelivr.com/package/npm/axios
                $packageLink = 'https://www.jsdelivr.com/package/npm/' . $package;
                $map['package'] = $packageLink;
                $data[] = [
                    'key' => $package,
                    'packageLink' => $packageLink,
                    'version' => $version,
                ];
            } else {
//                dd($packageData);
            }

        }

        return $this->render('credit/index.html.twig', [
            'data' => $data,
            'controller_name' => 'CreditController',
        ]);
    }
}
