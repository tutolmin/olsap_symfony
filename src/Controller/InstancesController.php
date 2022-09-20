<?php

namespace App\Controller;

use App\Entity\Instances;
use App\Form\InstancesType;
use App\Repository\InstancesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\LxcManager;

#[Route('/instances')]
class InstancesController extends AbstractController
{
    private LxcManager $lxcManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxcManager,
//      EntityManagerInterface $entityManager, MessageBusInterface $bus,
//      LoggerInterface $logger
        )
    {

        $this->lxcManager = $lxcManager;
    }

    #[Route('/', name: 'app_instances_index', methods: ['GET'])]
    public function index(InstancesRepository $instancesRepository): Response
    {
        return $this->render('instances/index.html.twig', [
            'instances' => $instancesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instances_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InstancesRepository $instancesRepository): Response
    {
        $instance = new Instances();
        $form = $this->createForm(InstancesType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instancesRepository->add($instance, true);

            return $this->redirectToRoute('app_instances_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instances/new.html.twig', [
            'instance' => $instance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instances_show', methods: ['GET'])]
    public function show(Instances $instance): Response
    {
        $addrs = array();
        foreach($instance->getAddresses()->getValues() as $se)
          $addrs[] = $se->getPort() . ":" .$se->getIp();

        return $this->render('instances/show.html.twig', [
            'instance' => $instance,
            'addrs' => $instance->getAddressesCounter() .': '. implode( ', ', $addrs),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instances_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instances $instance, InstancesRepository $instancesRepository): Response
    {
        $form = $this->createForm(InstancesType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instancesRepository->add($instance, true);

            return $this->redirectToRoute('app_instances_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instances/edit.html.twig', [
            'instance' => $instance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/start', name: 'app_instances_start', methods: ['POST'])]
    public function start(Request $request, Instances $instance, InstancesRepository $instancesRepository): Response
    {
        if ($this->isCsrfTokenValid('start'.$instance->getId(), $request->request->get('_token'))) {

            $this->lxcManager->startInstance($instance->getName());
        }

        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/stop', name: 'app_instances_stop', methods: ['POST'])]
    public function stop(Request $request, Instances $instance, InstancesRepository $instancesRepository): Response
    {
        if ($this->isCsrfTokenValid('stop'.$instance->getId(), $request->request->get('_token'))) {

            $this->lxcManager->stopInstance($instance->getName());
        }

        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/restart', name: 'app_instances_restart', methods: ['POST'])]
    public function restart(Request $request, Instances $instance, InstancesRepository $instancesRepository): Response
    {
        if ($this->isCsrfTokenValid('restart'.$instance->getId(), $request->request->get('_token'))) {

            $this->lxcManager->restartInstance($instance->getName());
        }

        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_instances_delete', methods: ['POST'])]
    public function delete(Request $request, Instances $instance, InstancesRepository $instancesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instance->getId(), $request->request->get('_token'))) {
            $instancesRepository->remove($instance, true);
        }

        return $this->redirectToRoute('app_instances_index', [], Response::HTTP_SEE_OTHER);
    }
}
