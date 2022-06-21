<?php

namespace App\Controller;

use App\Entity\Technologies;
use App\Form\TechnologiesType;
use App\Repository\TechnologiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/technologies')]
class TechnologiesController extends AbstractController
{
    #[Route('/', name: 'app_technologies_index', methods: ['GET'])]
    public function index(TechnologiesRepository $technologiesRepository): Response
    {
        return $this->render('technologies/index.html.twig', [
            'technologies' => $technologiesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_technologies_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TechnologiesRepository $technologiesRepository): Response
    {
        $technology = new Technologies();
        $form = $this->createForm(TechnologiesType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technologiesRepository->add($technology, true);

            return $this->redirectToRoute('app_technologies_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('technologies/new.html.twig', [
            'technology' => $technology,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_technologies_show', methods: ['GET'])]
    public function show(Technologies $technology): Response
    {
        return $this->render('technologies/show.html.twig', [
            'technology' => $technology,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_technologies_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Technologies $technology, TechnologiesRepository $technologiesRepository): Response
    {
        $form = $this->createForm(TechnologiesType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technologiesRepository->add($technology, true);

            return $this->redirectToRoute('app_technologies_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('technologies/edit.html.twig', [
            'technology' => $technology,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_technologies_delete', methods: ['POST'])]
    public function delete(Request $request, Technologies $technology, TechnologiesRepository $technologiesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$technology->getId(), $request->request->get('_token'))) {
            $technologiesRepository->remove($technology, true);
        }

        return $this->redirectToRoute('app_technologies_index', [], Response::HTTP_SEE_OTHER);
    }
}
