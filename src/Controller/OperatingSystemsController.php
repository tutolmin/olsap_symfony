<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\OperatingSystems;
use App\Form\OperatingSystemsType;
use App\Repository\OperatingSystemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OperatingSystemsManager;

#[Route('/operating/systems')]
class OperatingSystemsController extends AbstractController {

    private LoggerInterface $logger;
    private OperatingSystemsManager $osManager;

    public function __construct(LoggerInterface $logger,
            OperatingSystemsManager $osManager) {
        $this->logger = $logger;
        $this->osManager = $osManager;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_operating_systems_index', methods: ['GET'])]
    public function index(OperatingSystemsRepository $operatingSystemsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('operating_systems/index.html.twig', [
            'operating_systems' => $operatingSystemsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_operating_systems_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        $this->logger->debug(__METHOD__);

        $operatingSystem = new OperatingSystems();
        $form = $this->createForm(OperatingSystemsType::class, $operatingSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->osManager->addOperatingSystem($operatingSystem);
            return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operating_systems/new.html.twig', [
            'operating_system' => $operatingSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_operating_systems_show', methods: ['GET'])]
    public function show(OperatingSystems $operatingSystem): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('operating_systems/show.html.twig', [
            'operating_system' => $operatingSystem,
            'sessions' => $operatingSystem->getSessionsCounter(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_operating_systems_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OperatingSystems $operatingSystem): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(OperatingSystemsType::class, $operatingSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->osManager->editOperatingSystem($operatingSystem);
            return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operating_systems/edit.html.twig', [
            'operating_system' => $operatingSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_operating_systems_delete', methods: ['POST'])]
    public function delete(Request $request, OperatingSystems $operatingSystem): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete' . $operatingSystem->getId(),
                        strval($request->request->get('_token')))) {
            $this->osManager->removeOperatingSystem($operatingSystem);
        }

        return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_operating_systems_delete_cascade', methods: ['POST'])]
    public function delete_cascade(Request $request, OperatingSystems $operatingSystem): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete_cascade' . $operatingSystem->getId(),
                        strval($request->request->get('_token')))) {
            $this->osManager->removeOperatingSystem($operatingSystem, true);
        }

        return $this->redirectToRoute('app_operating_systems_index', [], Response::HTTP_SEE_OTHER);
    }
}
