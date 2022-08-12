<?php

namespace App\Controller;

use App\Entity\Ports;
use App\Form\PortsType;
use App\Repository\PortsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ports')]
class PortsController extends AbstractController
{
    #[Route('/', name: 'app_ports_index', methods: ['GET'])]
    public function index(PortsRepository $portsRepository): Response
    {
        return $this->render('ports/index.html.twig', [
            'ports' => $portsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PortsRepository $portsRepository): Response
    {
        $port = new Ports();
        $form = $this->createForm(PortsType::class, $port);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portsRepository->add($port, true);

            return $this->redirectToRoute('app_ports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ports/new.html.twig', [
            'port' => $port,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ports_show', methods: ['GET'])]
    public function show(Ports $port): Response
    {
        return $this->render('ports/show.html.twig', [
            'port' => $port,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ports $port, PortsRepository $portsRepository): Response
    {
        $form = $this->createForm(PortsType::class, $port);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portsRepository->add($port, true);

            return $this->redirectToRoute('app_ports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ports/edit.html.twig', [
            'port' => $port,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ports_delete', methods: ['POST'])]
    public function delete(Request $request, Ports $port, PortsRepository $portsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$port->getId(), $request->request->get('_token'))) {
            $portsRepository->remove($port, true);
        }

        return $this->redirectToRoute('app_ports_index', [], Response::HTTP_SEE_OTHER);
    }
}
