<?php

namespace App\Controller;

use App\Entity\Official;
use App\Entity\Term;
use App\Form\TermType;
use App\Repository\TermRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/term/crud')]
class TermCrudController extends AbstractController
{
    #[Route('/', name: 'app_term_crud_index', methods: ['GET'])]
    public function crud_index(TermRepository $termRepository): Response
    {
        return $this->render('term_crud/index.html.twig', [
            'terms' => $termRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_term_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $term = new Term();
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($term);
            $entityManager->flush();

            return $this->redirectToRoute('app_term_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('term_crud/new.html.twig', [
            'term' => $term,
            'form' => $form,
        ]);
    }

    #[Route('/browse',  methods: ['GET'], options: ['label' => "Browse (simple-dt)"])]
    public function index(TermRepository $termRepository): Response
    {
        return $this->render('term_crud/index.html.twig', [
            'terms' => $termRepository->findAll(),
            'class' => Official::class
        ]);
    }

    #[Route('/{id}', name: 'app_term_crud_show', methods: ['GET'])]
    public function show(Term $term): Response
    {
        return $this->render('term_crud/show.html.twig', [
            'term' => $term,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_term_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Term $term, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_term_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('term_crud/edit.html.twig', [
            'term' => $term,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_term_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Term $term, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$term->getId(), $request->request->get('_token'))) {
            $entityManager->remove($term);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_term_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
