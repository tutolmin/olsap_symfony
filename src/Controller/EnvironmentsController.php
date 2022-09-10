<?php

namespace App\Controller;

use App\Entity\Environments;
use App\Form\EnvironmentsType;
use App\Repository\EnvironmentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SessionManager;

#[Route('/environments')]
class EnvironmentsController extends AbstractController
{
    private $sessionManager;

    // InstanceTypes repo
//    private $sessionStatusesRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( SessionManager $sessionManager)
    {   
//        $this->entityManager = $entityManager;
        $this->sessionManager = $sessionManager;

        // get the SessionStatuses repository
//        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
    }

    #[Route('/', name: 'app_environments_index', methods: ['GET'])]
    public function index(EnvironmentsRepository $environmentsRepository): Response
    {
        return $this->render('environments/index.html.twig', [
            'environments' => $environmentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_environments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnvironmentsRepository $environmentsRepository): Response
    {
        $environment = new Environments();
        $form = $this->createForm(EnvironmentsType::class, $environment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environmentsRepository->add($environment, true);

            return $this->redirectToRoute('app_environments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('environments/new.html.twig', [
            'environment' => $environment,
            'form' => $form,
        ]);
    }

    #[Route('/{hash}', name: 'app_environments_display', methods: ['GET'], requirements: ['hash' => '[\d\w]{8}'])]
    public function display(Environments $environment): Response
    {
        return $this->render('environments/display.html.twig', [
            'environment' => $environment,
            'port' => $environment->getInstance()->getAddresses()[0]->getPort(),
	    'task_description' => $environment->getTask()->getDescription(),
	    'session_url' => $this->generateUrl('app_sessions_display', ['hash' => $environment->getSession()->getHash()]),
        ]);
    }

    #[Route('/{hash}/verify', name: 'app_environments_verify', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function verify(Environments $environment): Response
    {
        $this->sessionManager->setEnvironmentStatus($environment, "Complete");

        return $this->redirectToRoute('app_sessions_display', ['hash' => $environment->getSession()->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_environments_show', methods: ['GET'])]
    public function show(Environments $environment): Response
    {
        return $this->render('environments/show.html.twig', [
            'environment' => $environment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_environments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Environments $environment, EnvironmentsRepository $environmentsRepository): Response
    {
        $form = $this->createForm(EnvironmentsType::class, $environment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environmentsRepository->add($environment, true);

            return $this->redirectToRoute('app_environments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('environments/edit.html.twig', [
            'environment' => $environment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_environments_delete', methods: ['POST'])]
    public function delete(Request $request, Environments $environment, EnvironmentsRepository $environmentsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$environment->getId(), $request->request->get('_token'))) {
            $environmentsRepository->remove($environment, true);
        }

        return $this->redirectToRoute('app_environments_index', [], Response::HTTP_SEE_OTHER);
    }
}
