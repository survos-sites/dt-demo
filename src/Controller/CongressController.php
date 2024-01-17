<?php

namespace App\Controller;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use App\Entity\Official;
use App\Form\OfficialType;
use App\Repository\OfficialRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Survos\ApiGrid\State\MeiliSearchStateProvider;
use Survos\InspectionBundle\Services\InspectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/congress')]
class CongressController extends AbstractController
{

    public function __construct()
    {
    }

    #[Route('/crud_index', name: 'congress_crud_index', methods: ['GET'], options: ['label' => ['en' => 'Simple Datatables']])]
    public function congressSimpleDatatables(OfficialRepository $officialRepository): Response
    {
        return $this->render('congress/simple_datatables.html.twig', [
            'officials' => $officialRepository->findAll(),
            'useStimulus' => false
        ]);
    }

    #[Route('/simple_datatables', methods: ['GET'])]
    public function simple_datatables(OfficialRepository $officialRepository): Response
    {
        return $this->render('congress/simple_datatables.html.twig', [
            'officials' => $officialRepository->findAll(),
        ]);
    }

    #[Route('/grid', methods: ['GET'])]
    public function grid(OfficialRepository $officialRepository): Response
    {
        return $this->render('congress/grid.html.twig', [
            'data' => $officialRepository->findAll(),
        ]);
    }


    #[Route('/api_grid',  name: 'congress_api_grid', methods: ['GET'], options: ['label' => "Browse (api_grid)"])]
    public function api_grid(Request $request, InspectionService $inspectionService): Response
    {
        $class = Official::class;
        $endpoints = $inspectionService->getAllUrlsForResource($class);
        $apiRoute = $request->get('doctrine', false) ? 'doctrine-officials' : 'meili-officials';
//        dd($endpoints);
//        $apiCall = $endpoints[$useMeili ? MeiliSearchStateProvider::class : CollectionProvider::class];

        return $this->render('congress/browse.html.twig', [
            'class' => Official::class,
            'apiRoute' => $apiRoute,
            'apiCall' => $apiCall??null
        ]);
    }


    #[Route('/new', name: 'app_congress_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $official = new Official();
        $form = $this->createForm(OfficialType::class, $official);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($official);
            $entityManager->flush();

            return $this->redirectToRoute('congress_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('congress/new.html.twig', [
            'official' => $official,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_congress_show', methods: ['GET'])]
    public function show(Official $official, FilesystemOperator $defaultStorage): Response
    {
//        foreach ($official->getImageCodes() as $imageCodeData) {
//            $path = $imageCodeData['code'];
//            // or fileExists?
//            $mimeType = $defaultStorage->mimeType($path);
//            $image = $defaultStorage->has($path);
////            dd($image, $mimeType);
//        }
        return $this->render('congress/show.html.twig', [
            'official' => $official,
        ]);
    }
    #[Route('/{id}/refresh', name: 'congress_refresh', methods: ['GET', 'POST'])]
    public function refresh(Request $request, Official $official, CacheManager $cacheManager): Response
    {
        foreach ($official->getImageCodes()??[] as $imageData) {
            $cacheManager->remove($imageData['code']);
        }
        return $this->redirectToRoute('app_congress_show', $official->getrp());


    }


    #[Route('/{id}/edit', name: 'app_congress_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Official $official, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfficialType::class, $official);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('congress_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('congress/edit.html.twig', [
            'official' => $official,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_congress_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Official $official, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$official->getId(), $request->request->get('_token'))) {
            $entityManager->remove($official);
            $entityManager->flush();
        }

        return $this->redirectToRoute('congress_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
