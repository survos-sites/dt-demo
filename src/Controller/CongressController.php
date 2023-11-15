<?php

namespace App\Controller;

use App\Entity\Official;
use App\Form\OfficialType;
use App\Repository\OfficialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/congress')]
class CongressController extends AbstractController
{
    #[Route('/crud_index', name: 'congress_crud_index', methods: ['GET'], options: ['label' => ['en' => 'Simple Datatables']])]
    public function crud(OfficialRepository $officialRepository): Response
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
    public function api_grid(): Response
    {
        return $this->render('congress/browse.html.twig', [
            'class' => Official::class
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
    public function show(Official $official): Response
    {
        return $this->render('congress/show.html.twig', [
            'official' => $official,
        ]);
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
