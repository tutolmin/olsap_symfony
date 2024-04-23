<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\HardwareProfiles;
use App\Form\HardwareProfilesType;
use App\Repository\HardwareProfilesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\HardwareProfilesManager;

#[Route('/hardware/profiles')]
class HardwareProfilesController extends AbstractController {

    private LoggerInterface $logger;
    private HardwareProfilesManager $hpManager;

    public function __construct(LoggerInterface $logger,
            HardwareProfilesManager $hpManager) {
        $this->logger = $logger;
        $this->hpManager = $hpManager;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_hardware_profiles_index', methods: ['GET'])]
    public function index(HardwareProfilesRepository $hardwareProfilesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('hardware_profiles/index.html.twig', [
            'hardware_profiles' => $hardwareProfilesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hardware_profiles_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->logger->debug(__METHOD__);

        $hardwareProfile = new HardwareProfiles();
        $form = $this->createForm(HardwareProfilesType::class, $hardwareProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->hpManager->addHardwareProfile($hardwareProfile);
            return $this->redirectToRoute('app_hardware_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hardware_profiles/new.html.twig', [
            'hardware_profile' => $hardwareProfile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hardware_profiles_show', methods: ['GET'])]
    public function show(HardwareProfiles $hardwareProfile): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('hardware_profiles/show.html.twig', [
            'hardware_profile' => $hardwareProfile,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hardware_profiles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HardwareProfiles $hardwareProfile): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(HardwareProfilesType::class, $hardwareProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->hpManager->editHardwareProfile($hardwareProfile);
            return $this->redirectToRoute('app_hardware_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hardware_profiles/edit.html.twig', [
            'hardware_profile' => $hardwareProfile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_hardware_profiles_delete', methods: ['POST'])]
    public function delete(Request $request, HardwareProfiles $hardwareProfile): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete' . $hardwareProfile->getId(),
                        strval($request->request->get('_token')))) {
            $this->hpManager->removeHardwareProfile($hardwareProfile);
        }

        return $this->redirectToRoute('app_hardware_profiles_index', [], Response::HTTP_SEE_OTHER);
    }
/*
    #[Route('/{id}', name: 'app_hardware_profiles_delete_cascade', methods: ['POST'])]
    public function delete_cascade(Request $request, HardwareProfiles $hardwareProfile): Response {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete_cascade' . $hardwareProfile->getId(),
                        strval($request->request->get('_token')))) {
            $this->hpManager->removeHardwareProfile($hardwareProfile, true);
        }

        return $this->redirectToRoute('app_hardware_profiles_index', [], Response::HTTP_SEE_OTHER);
    }
 * 
 */
}
