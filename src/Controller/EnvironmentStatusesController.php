<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\EnvironmentStatuses;
use App\Form\EnvironmentStatusesType;
use App\Repository\EnvironmentStatusesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EnvironmentStatusesManager;

#[Route('/environment/statuses')]
class EnvironmentStatusesController extends AbstractController
{
    private LoggerInterface $logger;
    private EnvironmentStatusesManager $environmentStatusesManager;
    public function __construct(LoggerInterface $logger,
            EnvironmentStatusesManager $environmentStatusesManager)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
        $this->environmentStatusesManager = $environmentStatusesManager;
    }

    #[Route('/', name: 'app_environment_statuses_index', methods: ['GET'])]
    public function index(EnvironmentStatusesRepository $environmentStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('environment_statuses/index.html.twig', [
            'environment_statuses' => $environmentStatusesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_environment_statuses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnvironmentStatusesRepository $environmentStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $environmentStatus = new EnvironmentStatuses();
        $form = $this->createForm(EnvironmentStatusesType::class, $environmentStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environmentStatusesRepository->add($environmentStatus, true);

            return $this->redirectToRoute('app_environment_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('environment_statuses/new.html.twig', [
            'environment_status' => $environmentStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_environment_statuses_show', methods: ['GET'])]
    public function show(EnvironmentStatuses $environmentStatus): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('environment_statuses/show.html.twig', [
            'environment_status' => $environmentStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_environment_statuses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnvironmentStatuses $environmentStatus, 
            EnvironmentStatusesRepository $environmentStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(EnvironmentStatusesType::class, $environmentStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environmentStatusesRepository->add($environmentStatus, true);

            return $this->redirectToRoute('app_environment_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('environment_statuses/edit.html.twig', [
            'environment_status' => $environmentStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_environment_statuses_delete', methods: ['POST'])]
    public function delete(Request $request, EnvironmentStatuses $environmentStatus): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$environmentStatus->getId(), strval($request->request->get('_token')))) {
//            $environmentStatusesRepository->remove($environmentStatus, true);
            $this->environmentStatusesManager->removeEnvironmentStatus($environmentStatus);
            }

        return $this->redirectToRoute('app_environment_statuses_index', [], Response::HTTP_SEE_OTHER);
    }
}
