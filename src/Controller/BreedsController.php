<?php

namespace App\Controller;

use App\Entity\Breeds;
use App\Form\BreedsType;
use App\Repository\BreedsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/breeds')]
class BreedsController extends AbstractController
{
    #[Route('/', name: 'app_breeds_index', methods: ['GET'])]
    public function index(BreedsRepository $breedsRepository): Response
    {
        return $this->render('breeds/index.html.twig', [
            'breeds' => $breedsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_breeds_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BreedsRepository $breedsRepository): Response
    {
        $breed = new Breeds();
        $form = $this->createForm(BreedsType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $breedsRepository->add($breed, true);

            return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('breeds/new.html.twig', [
            'breed' => $breed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_breeds_show', methods: ['GET'])]
    public function show(Breeds $breed): Response
    {
        return $this->render('breeds/show.html.twig', [
            'breed' => $breed,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_breeds_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Breeds $breed, BreedsRepository $breedsRepository): Response
    {
        $form = $this->createForm(BreedsType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $breedsRepository->add($breed, true);

            return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('breeds/edit.html.twig', [
            'breed' => $breed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_breeds_delete', methods: ['POST'])]
    public function delete(Request $request, Breeds $breed, BreedsRepository $breedsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$breed->getId(), $request->request->get('_token'))) {
            $breedsRepository->remove($breed, true);
        }

        return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
    }
}
