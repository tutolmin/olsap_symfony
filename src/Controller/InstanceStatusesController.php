<?php

namespace App\Controller;

use App\Entity\InstanceStatuses;
use App\Form\InstanceStatusesType;
use App\Repository\InstanceStatusesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instance/statuses')]
class InstanceStatusesController extends AbstractController
{
    #[Route('/', name: 'app_instance_statuses_index', methods: ['GET'])]
    public function index(InstanceStatusesRepository $instanceStatusesRepository): Response
    {
        return $this->render('instance_statuses/index.html.twig', [
            'instance_statuses' => $instanceStatusesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instance_statuses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InstanceStatusesRepository $instanceStatusesRepository): Response
    {
        $instanceStatus = new InstanceStatuses();
        $form = $this->createForm(InstanceStatusesType::class, $instanceStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instanceStatusesRepository->add($instanceStatus, true);

            return $this->redirectToRoute('app_instance_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instance_statuses/new.html.twig', [
            'instance_status' => $instanceStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instance_statuses_show', methods: ['GET'])]
    public function show(InstanceStatuses $instanceStatus): Response
    {
        return $this->render('instance_statuses/show.html.twig', [
            'instance_status' => $instanceStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instance_statuses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InstanceStatuses $instanceStatus, InstanceStatusesRepository $instanceStatusesRepository): Response
    {
        $form = $this->createForm(InstanceStatusesType::class, $instanceStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instanceStatusesRepository->add($instanceStatus, true);

            return $this->redirectToRoute('app_instance_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instance_statuses/edit.html.twig', [
            'instance_status' => $instanceStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instance_statuses_delete', methods: ['POST'])]
    public function delete(Request $request, InstanceStatuses $instanceStatus, InstanceStatusesRepository $instanceStatusesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instanceStatus->getId(), $request->request->get('_token'))) {
            $instanceStatusesRepository->remove($instanceStatus, true);
        }

        return $this->redirectToRoute('app_instance_statuses_index', [], Response::HTTP_SEE_OTHER);
    }
}
