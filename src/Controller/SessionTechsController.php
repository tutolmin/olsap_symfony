<?php

namespace App\Controller;

use App\Entity\SessionTechs;
use App\Form\SessionTechsType;
use App\Repository\SessionTechsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session/techs')]
class SessionTechsController extends AbstractController
{
    #[Route('/', name: 'app_session_techs_index', methods: ['GET'])]
    public function index(SessionTechsRepository $sessionTechsRepository): Response
    {
        return $this->render('session_techs/index.html.twig', [
            'session_techs' => $sessionTechsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_session_techs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionTechsRepository $sessionTechsRepository): Response
    {
        $sessionTech = new SessionTechs();
        $form = $this->createForm(SessionTechsType::class, $sessionTech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionTechsRepository->add($sessionTech, true);

            return $this->redirectToRoute('app_session_techs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_techs/new.html.twig', [
            'session_tech' => $sessionTech,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_techs_show', methods: ['GET'])]
    public function show(SessionTechs $sessionTech): Response
    {
        return $this->render('session_techs/show.html.twig', [
            'session_tech' => $sessionTech,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_techs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionTechs $sessionTech, SessionTechsRepository $sessionTechsRepository): Response
    {
        $form = $this->createForm(SessionTechsType::class, $sessionTech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionTechsRepository->add($sessionTech, true);

            return $this->redirectToRoute('app_session_techs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session_techs/edit.html.twig', [
            'session_tech' => $sessionTech,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_techs_delete', methods: ['POST'])]
    public function delete(Request $request, SessionTechs $sessionTech, SessionTechsRepository $sessionTechsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sessionTech->getId(), $request->request->get('_token'))) {
            $sessionTechsRepository->remove($sessionTech, true);
        }

        return $this->redirectToRoute('app_session_techs_index', [], Response::HTTP_SEE_OTHER);
    }
}
