<?php

namespace App\Controller;

use App\Entity\OperatingSystems;
use App\Form\OperatingSystems1Type;
use App\Repository\OperatingSystemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/operating/systems')]
class OperatingSystemsController extends AbstractController
{
    #[Route('/', name: 'app_operating_systems_index', methods: ['GET'])]
    public function index(OperatingSystemsRepository $operatingSystemsRepository): Response
    {
        return $this->render('operating_systems/index.html.twig', [
            'operating_systems' => $operatingSystemsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_operating_systems_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OperatingSystemsRepository $operatingSystemsRepository): Response
    {
        $operatingSystem = new OperatingSystems();
        $form = $this->createForm(OperatingSystems1Type::class, $operatingSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operatingSystemsRepository->add($operatingSystem, true);

            return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operating_systems/new.html.twig', [
            'operating_system' => $operatingSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_operating_systems_show', methods: ['GET'])]
    public function show(OperatingSystems $operatingSystem): Response
    {
        return $this->render('operating_systems/show.html.twig', [
            'operating_system' => $operatingSystem,
            'sessions' => $operatingSystem->getSessionsCounter(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_operating_systems_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OperatingSystems $operatingSystem, OperatingSystemsRepository $operatingSystemsRepository): Response
    {
        $form = $this->createForm(OperatingSystems1Type::class, $operatingSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operatingSystemsRepository->add($operatingSystem, true);

            return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operating_systems/edit.html.twig', [
            'operating_system' => $operatingSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_operating_systems_delete', methods: ['POST'])]
    public function delete(Request $request, OperatingSystems $operatingSystem, OperatingSystemsRepository $operatingSystemsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$operatingSystem->getId(), $request->request->get('_token'))) {
            $operatingSystemsRepository->remove($operatingSystem, true);
        }

        return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
    }
}
