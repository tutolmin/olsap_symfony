<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\SessionOses;
use App\Form\SessionOsesType;
use App\Repository\SessionOsesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session/oses')]
class SessionOsesController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_session_oses_index', methods: ['GET'])]
    public function index(SessionOsesRepository $sessionOsesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('session_oses/index.html.twig', [
            'session_oses' => $sessionOsesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_session_oses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionOsesRepository $sessionOsesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $sessionOs = new SessionOses();
        $form = $this->createForm(SessionOsesType::class, $sessionOs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionOsesRepository->add($sessionOs, true);

            return $this->redirectToRoute('app_session_oses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_oses/new.html.twig', [
            'session_os' => $sessionOs,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_oses_show', methods: ['GET'])]
    public function show(SessionOses $sessionOs): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('session_oses/show.html.twig', [
            'session_os' => $sessionOs,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_oses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionOses $sessionOs, SessionOsesRepository $sessionOsesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(SessionOsesType::class, $sessionOs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionOsesRepository->add($sessionOs, true);

            return $this->redirectToRoute('app_session_oses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_oses/edit.html.twig', [
            'session_os' => $sessionOs,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_oses_delete', methods: ['POST'])]
    public function delete(Request $request, SessionOses $sessionOs, SessionOsesRepository $sessionOsesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$sessionOs->getId(), strval($request->request->get('_token')))) {
            $sessionOsesRepository->remove($sessionOs, true);
        }

        return $this->redirectToRoute('app_session_oses_index', [], Response::HTTP_SEE_OTHER);
    }
}
