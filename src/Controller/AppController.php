<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{

    #[Route(path: '/', name: 'app_homepage', options: ['sitemap' => ['priority' => 1]])]
    public function homepage(): Response
    {
        // testing
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

    #[Route('/dexie', name: 'app_dexie')]
    public function dexie(): Response
    {
        return $this->render('app/dexie2.html.twig', [
            'controllerClass' => self::class
        ]);
    }

    #[Route('/wikidata', name: 'app_wikidata')]
    public function wikidata(): Response
    {

        $filename = __DIR__ . '/../../chunk2.gz';
        assert(file_exists($filename), $filename);

//        $handle = gzopen('somefile.gz', 'r');
//        while (!gzeof($handle)) {
//            $buffer = gzgets($handle, 4096);
//            echo $buffer;
//        }
//        gzclose($handle);

        $sfp = gzopen($filename, "r");
        $idx = 0;
        while ($line = fgets($sfp)) {
            if ($idx) {
                $line = trim($line, "\n,");
                if (!json_validate($line)) {
                    break; // because of the partial get

                }
                assert(json_validate($line), $line);
                $data = json_decode($line);
                if ($data->type <> 'item') {
                    dd($data);
                }
                foreach ($data->claims as $claimList) {
                    foreach ($claimList as $claim) {
//                        dd(claim: $claim, qualifiers: $claim->qualifiers);
                        foreach ($claim->qualifiers??[] as $propertyCode => $qualifier) {
//                            dump($propertyCode, $qualifier[0]);
                        }
                    }
                }
//                dd($data, $data->labels->en,$line);
            }
            $idx++;
        }
//        dd($idx . ' records searched');

        return $this->render('app/wikidata.html.twig', [
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
