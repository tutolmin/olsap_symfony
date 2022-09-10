<?php

namespace App\Controller;

use App\Entity\Sessions;
use App\Form\SessionsType;
use App\Repository\SessionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
//use App\Entity\SessionStatuses;
use App\Service\SessionManager;

#[Route('/sessions')]
class SessionsController extends AbstractController
{
    // Doctrine EntityManager
    private $entityManager;

    private $sessionManager;

    // InstanceTypes repo
//    private $sessionStatusesRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, SessionManager $sessionManager)
    {   
        $this->entityManager = $entityManager;
        $this->sessionManager = $sessionManager;

        // get the SessionStatuses repository
//        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
    }

    #[Route('/', name: 'app_sessions_index', methods: ['GET'])]
    public function index(SessionsRepository $sessionsRepository): Response
    {
        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sessions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionsRepository $sessionsRepository): Response
    {
        $session = new Sessions();
        $form = $this->createForm(SessionsType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionsRepository->add($session, true);

            return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sessions/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{hash}/start', name: 'app_sessions_start', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function start(Sessions $session): Response
    {
	$this->sessionManager->setSessionStatus($session, "Started");
/*
	$session->setStatus($this->sessionStatusesRepository->findOneByStatus("Started"));

        // Store item into the DB
        $this->entityManager->persist($session);
        $this->entityManager->flush();
*/
        return $this->redirectToRoute('app_sessions_display', ['hash' => $session->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{hash}/finish', name: 'app_sessions_finish', methods: ['POST'], requirements: ['hash' => '[\d\w]{8}'])]
    public function finish(Sessions $session): Response
    {
	$this->sessionManager->setSessionStatus($session, "Finished");
/*
	$session->setStatus($this->sessionStatusesRepository->findOneByStatus("Finished"));

        // Store item into the DB
        $this->entityManager->persist($session);
        $this->entityManager->flush();
*/
        return $this->redirectToRoute('app_sessions_display', ['hash' => $session->getHash()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{hash}', name: 'app_sessions_display', methods: ['GET'], requirements: ['hash' => '[\d\w]{8}'])]
    public function display(Sessions $session): Response
    {
        $envs = array();
        foreach($session->getEnvs()->getValues() as $se)
	  $envs[$se->getTask() . " @ " . $se->getInstance() . " : " . $se->getStatus()] = 
		$this->generateUrl('app_environments_display', ['hash' => $se->getHash()]);

        return $this->render('sessions/display.html.twig', [
            'session' => $session,
            'envs' => $envs,
        ]);
    }

    #[Route('/{id}', name: 'app_sessions_show', methods: ['GET'])]
    public function show(Sessions $session): Response
    {
        $techs = array();
        foreach($session->getSessionTechs()->getValues() as $st)
          $techs[] = $st->getTech();

        $oses = array();
        foreach($session->getSessionOses()->getValues() as $so)
          $oses[] = $so->getOs();

        $envs = array();
        foreach($session->getEnvs()->getValues() as $se)
          $envs[] = $se->getTask() . " @ " . $se->getInstance();

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

    #[Route('/{id}', name: 'app_sessions_delete', methods: ['POST'])]
    public function delete(Request $request, Sessions $session, SessionsRepository $sessionsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $sessionsRepository->remove($session, true);
        }

        return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
    }
}
