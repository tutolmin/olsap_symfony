<?php

namespace App\Controller;

use App\Entity\TaskTechs;
use App\Form\TaskTechsType;
use App\Repository\TaskTechsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task/techs')]
class TaskTechsController extends AbstractController
{
    #[Route('/', name: 'app_task_techs_index', methods: ['GET'])]
    public function index(TaskTechsRepository $taskTechsRepository): Response
    {
        return $this->render('task_techs/index.html.twig', [
            'task_techs' => $taskTechsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_techs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskTechsRepository $taskTechsRepository): Response
    {
        $taskTech = new TaskTechs();
        $form = $this->createForm(TaskTechsType::class, $taskTech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskTechsRepository->add($taskTech, true);

            return $this->redirectToRoute('app_task_techs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_techs/new.html.twig', [
            'task_tech' => $taskTech,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_techs_show', methods: ['GET'])]
    public function show(TaskTechs $taskTech): Response
    {
        return $this->render('task_techs/show.html.twig', [
            'task_tech' => $taskTech,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_techs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskTechs $taskTech, TaskTechsRepository $taskTechsRepository): Response
    {
        $form = $this->createForm(TaskTechsType::class, $taskTech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskTechsRepository->add($taskTech, true);

            return $this->redirectToRoute('app_task_techs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_techs/edit.html.twig', [
            'task_tech' => $taskTech,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_techs_delete', methods: ['POST'])]
    public function delete(Request $request, TaskTechs $taskTech, TaskTechsRepository $taskTechsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskTech->getId(), $request->request->get('_token'))) {
            $taskTechsRepository->remove($taskTech, true);
        }

        return $this->redirectToRoute('app_task_techs_index', [], Response::HTTP_SEE_OTHER);
    }
}
