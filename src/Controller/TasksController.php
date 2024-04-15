<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Tasks;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks')]
class TasksController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_tasks_index', methods: ['GET'])]
    public function index(TasksRepository $tasksRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasksRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TasksRepository $tasksRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tasksRepository->add($task, true);

            return $this->redirectToRoute('app_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tasks_show', methods: ['GET'])]
    public function show(Tasks $task): Response {
        $this->logger->debug(__METHOD__);

        $techs = array();
        foreach ($task->getTaskTechs()->getValues() as $tt) {
            $techs[] = $tt->getTech();
        }

        $oses = array();
        foreach ($task->getTaskOses()->getValues() as $to) {
            $oses[] = $to->getOs()->getAlias();
        }
        $combos = array();
        foreach ($task->getTaskInstanceTypes()->getValues() as $tit) {
            $combos[] = $tit->getInstanceType()->getCombo();
        }
        return $this->render('tasks/show.html.twig', [
                    'task' => $task,
                    'techs' => $task->getTechsCounter() . ': ' . implode(', ', $techs),
                    'oses' => $task->getOsesCounter() . ': ' . implode(', ', $oses),
                    'combos' => $task->getInstanceTypesCounter() . ': ' . implode(', ', $combos),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tasks $task, TasksRepository $tasksRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tasksRepository->add($task, true);

            return $this->redirectToRoute('app_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tasks_delete', methods: ['POST'])]
    public function delete(Request $request, Tasks $task, TasksRepository $tasksRepository): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$task->getId(), strval($request->request->get('_token')))) {
            $tasksRepository->remove($task, true);
        }

        return $this->redirectToRoute('app_tasks_index', [], Response::HTTP_SEE_OTHER);
    }
}
