<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Instances;
use App\Form\InstancesType;
use App\Repository\InstancesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\ORM\EntityManagerInterface;
use App\Service\LxcManager;

#[Route('/instances')]
class InstancesController extends AbstractController
{
    private LoggerInterface $logger;
    private LxcManager $lxcService;

    // Doctrine EntityManager
//    private EntityManagerInterface $entityManager;
    
    // Dependency injection of the EntityManagerInterface entity
    public function __construct(LoggerInterface $logger,
//            EntityManagerInterface $entityManager, 
            LxcManager $lxcService) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->lxcService = $lxcService;

//        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_instances_index', methods: ['GET'])]
    public function index(InstancesRepository $instanceRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('instances/index.html.twig', [
            'instances' => $instanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instances_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        $this->logger->debug(__METHOD__);

        $instance = new Instances();
        $form = $this->createForm(InstancesType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->debug("Number of Instances to create: " . $form->get('number')->getData());

            $this->logger->debug("Selected Instance type: " . $instance->getInstanceType());
            
            for ($i = 0; $i < $form->get('number')->getData(); $i++) {
                
                $this->lxcService->create($instance->getInstanceType()->getOs()->getAlias(),
                        $instance->getInstanceType()->getHwProfile()->getName());
            }
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
        $this->logger->debug(__METHOD__);

        $addrs = array();
        foreach ($instance->getAddresses()->getValues() as $se) {
            $addrs[] = $se->getPort() . ":" . $se->getIp();
        }

        return $this->render('instances/show.html.twig', [
            'instance' => $instance,
            'addrs' => $instance->getAddressesCounter() .': '. implode( ', ', $addrs),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instances_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instances $instance, InstancesRepository $instanceRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(InstancesType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instanceRepository->add($instance, true);

            return $this->redirectToRoute('app_instances_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instances/edit.html.twig', [
            'instance' => $instance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/start', name: 'app_instances_start', methods: ['POST'])]
    public function start(Request $request, Instances $instance): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('start'.$instance->getId(), strval($request->request->get('_token')))) {
            $this->lxcService->start($instance->getName());            
         }

        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/stop', name: 'app_instances_stop', methods: ['POST'])]
    public function stop(Request $request, Instances $instance): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('stop'.$instance->getId(), strval($request->request->get('_token')))) {       
            $this->lxcService->stop($instance->getName());            
        }

        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/restart', name: 'app_instances_restart', methods: ['POST'])]
    public function restart(Request $request, Instances $instance): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('restart'.$instance->getId(), strval($request->request->get('_token')))) {            
            $this->lxcService->restart($instance->getName());            
        }
            
        return $this->redirectToRoute('app_instances_show', ['id'=>$instance->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_instances_delete', methods: ['POST'])]
    public function delete(Request $request, Instances $instance): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete' . $instance->getId(), strval($request->request->get('_token')))) {
            $this->lxcService->deleteInstance($instance->getName());     
        }

        return $this->redirectToRoute('app_instances_index', [], Response::HTTP_SEE_OTHER);
    }
}
