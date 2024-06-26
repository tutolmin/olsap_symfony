<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Sessions;
use App\Form\SessionsType;
use App\Repository\SessionsRepository;
use App\Repository\SessionStatusesRepository;
use App\Entity\SessionStatuses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
//use App\Entity\Environments;
use App\Service\SessionManager;
//use Symfony\Component\Messenger\MessageBusInterface;
//use App\Message\SessionAction;

#[Route('/sessions')]
class SessionsController extends AbstractController
{
    private LoggerInterface $logger;

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    // Repositories
//    private $environmentRepository;

    private SessionManager $sessionManager;

    // Message bus
//    private $sessionBus;
//    
    // InstanceTypes repo
    /**
     * 
     * @var SessionStatusesRepository
     */
    private $sessionStatusesRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( SessionManager $sessionManager, 
	EntityManagerInterface $entityManager, LoggerInterface $logger
	)
    {   


        $this->entityManager = $entityManager;
        $this->sessionManager = $sessionManager;
//        $this->bus = $sessionBus;
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);


        // get the SessionStatuses repository
        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
//        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
    }

    #[Route('/', name: 'app_sessions_index', methods: ['GET'])]
    public function index(SessionsRepository $sessionsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessionsRepository->findAll(),
        ]);
    }
    
    #[Route('/_{hash}', name: 'app_sessions_index_search', methods: ['GET'], requirements: ['hash' => '[\d\w]{8}'])]
    public function indexSearch(SessionsRepository $sessionsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sessions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionsRepository $sessionsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $session = new Sessions();
        $form = $this->createForm(SessionsType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // We will use it for any timestamp type
            $timestamp = new \DateTimeImmutable('NOW');
            $session->setCreatedAt($timestamp);

            $session_status = $this->sessionStatusesRepository->findOneByStatus("New");
            $session->setStatus($session_status);
            
            $sessionsRepository->add($session, true);

            return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sessions/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{hash}/start', name: 'app_sessions_start', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function start(Request $request, Sessions $session): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('start' . $session->getHash(), strval($request->request->get('_token')))) {

//            $this->logger->debug($session->getOsesCounter());
//            $this->logger->debug($session->getTechsCounter());
            if ($session->getOsesCounter() == 0 || $session->getTechsCounter() == 0) {
                return $this->redirectToRoute('app_sessions_show', ['id' => $session->getId()], Response::HTTP_SEE_OTHER);
            }
            $this->sessionManager->start($session);
/*
            $this->sessionManager->setSessionStatus($session, "Started");

            $this->sessionManager->setSessionTimestamp($session, "started");

            // Start certain number of instances
            $start_envs = intval($this->getParameter('APP_START_ENVS'));
            for ($i = 0; $i < $start_envs; $i++) {

                $this->sessionManager->allocateEnvironment($session);
            }
*/
        }
        return $this->redirectToRoute('app_sessions_display', ['hash' => $session->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{hash}/finish', name: 'app_sessions_finish', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function finish(Request $request, Sessions $session): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('finish'.$session->getHash(), strval($request->request->get('_token')))) {
            $this->sessionManager->finish($session);
/*
	  // Skip all remaining envs
          foreach($session->getEnvs()->getValues() as $se) {

	    if( $se->getStatus() == "Deployed") {

//	      $this->sessionManager->releaseInstance($se->getInstance());

	      $this->sessionManager->setEnvironmentStatus($se, "Skipped");
              $this->sessionManager->setEnvironmentTimestamp($se, "skipped");

              $this->sessionManager->setSessionTimestamp($session, "started");
	    }
	  }

	  $this->sessionManager->setSessionStatus($session, "Finished");
 * 
 */
	}
        return $this->redirectToRoute('app_sessions_display', ['hash' => $session->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{hash}', name: 'app_sessions_display', methods: ['GET'], requirements: ['hash' => '[\d\w]{8}'])]
    public function display(Sessions $session): Response
    {
        $this->logger->debug(__METHOD__);

        $env_links = array();
        $env_names = array();
        foreach($session->getEnvs()->getValues() as $se) {
//	  $envs[$se->getTask() . " @ " . $se->getInstance() . " : " . $se->getStatus()] = 
	  $env_links[$se->getHash()] = 
		$this->generateUrl('app_environments_display', ['hash' => $se->getHash()]);
	  $env_names[$se->getHash()] = $se->__toString();
//		$this->generateUrl('app_environments_display', ['hash' => $se->getHash()]);
	}
        return $this->render('sessions/display.html.twig', [
            'session' => $session,
            'env_links' => $env_links,
            'env_names' => $env_names,
        ]);
    }

    #[Route('/{id}', name: 'app_sessions_show', methods: ['GET'])]
    public function show(Sessions $session): Response
    {
        $this->logger->debug(__METHOD__);

        $techs = array();
        foreach ($session->getSessionTechs()->getValues() as $st) {
            $techs[] = $st->getTech();
        }

        $oses = array();
        foreach ($session->getSessionOses()->getValues() as $so) {
            $oses[] = $so->getOs();
        }

        $envs = array();
        foreach ($session->getEnvs()->getValues() as $se) {
//          $envs[] = $se->getTask() . " @ " . $se->getInstance();
            $envs[] = $se;
        }

        return $this->render('sessions/show.html.twig', [
            'session' => $session,
            'techs' => $session->getTechsCounter() .': '. implode( ', ', $techs),
            'oses' => $session->getOsesCounter() .': '. implode( ', ', $oses),
            'envs' => $session->getEnvsCounter() .': '. implode( ', ', $envs),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sessions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sessions $session, SessionsRepository $sessionsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(SessionsType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionsRepository->add($session, true);

            return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sessions/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sessions_delete', methods: ['POST'])]
    public function delete(Request $request, Sessions $session): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete' . $session->getId(), strval($request->request->get('_token')))) {
            $this->sessionManager->removeSession($session);
        }

        return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
    }
}
