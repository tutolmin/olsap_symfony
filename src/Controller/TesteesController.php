<?php

namespace App\Controller;

use App\Entity\Testees;
use App\Form\TesteesType;
use App\Repository\TesteesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/testees')]
class TesteesController extends AbstractController
{
    #[Route('/', name: 'app_testees_index', methods: ['GET'])]
    public function index(TesteesRepository $testeesRepository): Response
    {
        return $this->render('testees/index.html.twig', [
            'testees' => $testeesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_testees_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TesteesRepository $testeesRepository): Response
    {
        $testee = new Testees();
        $form = $this->createForm(TesteesType::class, $testee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testeesRepository->add($testee, true);

            return $this->redirectToRoute('app_testees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('testees/new.html.twig', [
            'testee' => $testee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_testees_show', methods: ['GET'])]
    public function show(Testees $testee): Response
    {
        return $this->render('testees/show.html.twig', [
            'testee' => $testee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_testees_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Testees $testee, TesteesRepository $testeesRepository): Response
    {
        $form = $this->createForm(TesteesType::class, $testee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testeesRepository->add($testee, true);

            return $this->redirectToRoute('app_testees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('testees/edit.html.twig', [
            'testee' => $testee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_testees_delete', methods: ['POST'])]
    public function delete(Request $request, Testees $testee, TesteesRepository $testeesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$testee->getId(), $request->request->get('_token'))) {
            $testeesRepository->remove($testee, true);
        }

        return $this->redirectToRoute('app_testees_index', [], Response::HTTP_SEE_OTHER);
    }
}
