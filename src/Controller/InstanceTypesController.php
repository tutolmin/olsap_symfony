<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\InstanceTypes;
use App\Form\InstanceTypesType;
use App\Repository\InstanceTypesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\InstanceTypesManager;

#[Route('/instance/types')]
class InstanceTypesController extends AbstractController
{
    private LoggerInterface $logger;
    private InstanceTypesManager $instanceTypesManager;
    public function __construct(LoggerInterface $logger,
            InstanceTypesManager $instanceTypesManager)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
        $this->instanceTypesManager = $instanceTypesManager;
    }

    #[Route('/', name: 'app_instance_types_index', methods: ['GET'])]
    public function index(InstanceTypesRepository $instanceTypesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('instance_types/index.html.twig', [
            'instance_types' => $instanceTypesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instance_types_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InstanceTypesRepository $instanceTypesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $instanceType = new InstanceTypes();
        $form = $this->createForm(InstanceTypesType::class, $instanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instanceTypesRepository->add($instanceType, true);

            return $this->redirectToRoute('app_instance_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instance_types/new.html.twig', [
            'instance_type' => $instanceType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instance_types_show', methods: ['GET'])]
    public function show(InstanceTypes $instanceType): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('instance_types/show.html.twig', [
            'instance_type' => $instanceType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instance_types_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InstanceTypes $instanceType, InstanceTypesRepository $instanceTypesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(InstanceTypesType::class, $instanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instanceTypesRepository->add($instanceType, true);

            return $this->redirectToRoute('app_instance_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instance_types/edit.html.twig', [
            'instance_type' => $instanceType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instance_types_delete', methods: ['POST'])]
    public function delete(Request $request, InstanceTypes $instanceType): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$instanceType->getId(), strval($request->request->get('_token')))) {
            $this->instanceTypesManager->removeInstanceType($instanceType);
        }

        return $this->redirectToRoute('app_instance_types_index', [], Response::HTTP_SEE_OTHER);
    }
}
