<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Addresses;
use App\Form\AddressesType;
use App\Repository\AddressesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/addresses')]
class AddressesController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_addresses_index', methods: ['GET'])]
    public function index(AddressesRepository $addressesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('addresses/index.html.twig', [
            'addresses' => $addressesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_addresses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AddressesRepository $addressesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $address = new Addresses();
        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressesRepository->add($address, true);

            return $this->redirectToRoute('app_addresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('addresses/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_addresses_show', methods: ['GET'])]
    public function show(Addresses $address): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('addresses/show.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_addresses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Addresses $address, AddressesRepository $addressesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressesRepository->add($address, true);

            return $this->redirectToRoute('app_addresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('addresses/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_addresses_delete', methods: ['POST'])]
    public function delete(Request $request, Addresses $address, AddressesRepository $addressesRepository): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $addressesRepository->remove($address, true);
        }

        return $this->redirectToRoute('app_addresses_index', [], Response::HTTP_SEE_OTHER);
    }
}
