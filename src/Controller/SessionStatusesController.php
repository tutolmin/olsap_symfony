<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\SessionStatuses;
use App\Form\SessionStatusesType;
use App\Repository\SessionStatusesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session/statuses')]
class SessionStatusesController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_session_statuses_index', methods: ['GET'])]
    public function index(SessionStatusesRepository $sessionStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('session_statuses/index.html.twig', [
            'session_statuses' => $sessionStatusesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_session_statuses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionStatusesRepository $sessionStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $sessionStatus = new SessionStatuses();
        $form = $this->createForm(SessionStatusesType::class, $sessionStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionStatusesRepository->add($sessionStatus, true);

            return $this->redirectToRoute('app_session_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_statuses/new.html.twig', [
            'session_status' => $sessionStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_statuses_show', methods: ['GET'])]
    public function show(SessionStatuses $sessionStatus): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('session_statuses/show.html.twig', [
            'session_status' => $sessionStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_statuses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionStatuses $sessionStatus, SessionStatusesRepository $sessionStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(SessionStatusesType::class, $sessionStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionStatusesRepository->add($sessionStatus, true);

            return $this->redirectToRoute('app_session_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_statuses/edit.html.twig', [
            'session_status' => $sessionStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_statuses_delete', methods: ['POST'])]
    public function delete(Request $request, SessionStatuses $sessionStatus, SessionStatusesRepository $sessionStatusesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$sessionStatus->getId(), strval($request->request->get('_token')))) {
            $sessionStatusesRepository->remove($sessionStatus, true);
        }

        return $this->redirectToRoute('app_session_statuses_index', [], Response::HTTP_SEE_OTHER);
    }
}
