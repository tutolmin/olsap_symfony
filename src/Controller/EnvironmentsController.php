<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Environments;
use App\Form\EnvironmentsType;
use App\Repository\EnvironmentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SessionManager;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SessionAction;
use App\Service\EnvironmentManager;

#[Route('/environments')]
class EnvironmentsController extends AbstractController
{
    private $logger;
    private $sessionManager;
    private $environmentService;
    private $sessionBus;

    // InstanceTypes repo
//    private $sessionStatusesRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( SessionManager $sessionManager, MessageBusInterface $sessionBus,
	LoggerInterface $logger, EnvironmentManager $environmentService)
    {   

//        $this->entityManager = $entityManager;
        $this->sessionManager = $sessionManager;
        $this->environmentService = $environmentService;
        $this->sessionBus = $sessionBus;
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        // get the SessionStatuses repository
//        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
    }

    #[Route('/', name: 'app_environments_index', methods: ['GET'])]
    public function index(EnvironmentsRepository $environmentsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('environments/index.html.twig', [
            'environments' => $environmentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_environments_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->logger->debug(__METHOD__);

        $environment = new Environments();
        $form = $this->createForm(EnvironmentsType::class, $environment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->debug("Number of Environments to create: " . $form->get('number')->getData());

            $this->logger->debug("Selected Task: " . $environment->getTask() .
                    " Session: " . $environment->getSession());

            for ($i = 0; $i < $form->get('number')->getData(); $i++) {

                if ($environment->getSession()) {
                    $this->environmentService->createEnvironment($environment->getTask()->getId(),
                            $environment->getSession()->getId());
                } else {
                    $this->environmentService->createEnvironment($environment->getTask()->getId(), null);
                }
            }

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
        $this->logger->debug(__METHOD__);

	$this->environmentService->setEnvironmentTimestamp($environment, "started");

	// Some envs (Skipped/Verified) might not have linked instances
	$port = "";
	if($environment->getInstance()){
	  $port = $environment->getInstance()->getAddresses()[0]->getPort();
        }
        $session_url = "_session";
        if($environment->getSession()){
            $session_url = $environment->getSession()->getHash();
        }
        return $this->render('environments/display.html.twig', [
            'environment' => $environment,
            'test_username' => $this->getParameter('app.username'),
	    'skip_limit' => $this->getParameter('app.skip_envs'),
            'public_ip' => $this->getParameter('app.public_ip'),
            'port' => $port,
	    'task_description' => $environment->getTask()->getDescription(),
	    'session_url' => $session_url,
        ]);
    }

    #[Route('/{hash}/skip', name: 'app_environments_skip', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function skip(Request $request, Environments $environment): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('skip'.$environment->getHash(), $request->request->get('_token'))) {

	  // Release instance
	  $instance = $environment->getInstance();
	  $this->environmentService->releaseInstance($instance);

	  $this->environmentService->setEnvironmentStatus($environment, "Skipped");

 	  $this->environmentService->setEnvironmentTimestamp($environment, "skipped");

	  // Allocate new environment for a session
	  $this->sessionManager->allocateEnvironment($environment->getSession());
//        $this->bus->dispatch(new SessionAction(["action" => "allocateEnvironment", "session_id" => $environment->getSession()->getId()]));
	}

        return $this->redirectToRoute('app_sessions_display', ['hash' => $environment->getSession()->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{hash}/verify', name: 'app_environments_verify', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function verify(Request $request, Environments $environment): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('verify'.$environment->getHash(), $request->request->get('_token'))) {

	  $this->environmentService->setEnvironmentStatus($environment, "Verified");

	  $this->environmentService->setEnvironmentTimestamp($environment, "verified");

	  // Verify specified environment
	  $this->sessionBus->dispatch(new SessionAction(["action" => "verifyEnvironment", "environment_id" => $environment->getId()]));

	  // Allocate new environment for a session
//        $this->bus->dispatch(new SessionAction(["action" => "allocateEnvironment", "session_id" => $environment->getSession()->getId()]));
	}

        return $this->redirectToRoute('app_sessions_display', ['hash' => $environment->getSession()->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_environments_show', methods: ['GET'])]
    public function show(Environments $environment): Response
    {
        $this->logger->debug(__METHOD__);

	// Some envs (Skipped/Verified) might not have linked instances
	$port = "";
	if($environment->getInstance()){
	  $port = $environment->getInstance()->getAddresses()[0]->getPort();
        }
        return $this->render('environments/show.html.twig', [
            'test_username' => $this->getParameter('app.username'),            
            'public_ip' => $this->getParameter('app.public_ip'),
            'port' => $port,
            'environment' => $environment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_environments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Environments $environment, 
            EnvironmentsRepository $environmentsRepository): Response
    {
        $this->logger->debug(__METHOD__);

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
    public function delete(Request $request, Environments $environment): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete' . $environment->getId(), $request->request->get('_token'))) {
            $this->environmentService->deleteEnvironment($environment);
        }

        return $this->redirectToRoute('app_environments_index', [], Response::HTTP_SEE_OTHER);
    }
}
