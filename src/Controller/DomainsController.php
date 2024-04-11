<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Domains;
use App\Form\DomainsType;
use App\Repository\DomainsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/domains')]
class DomainsController extends AbstractController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_domains_index', methods: ['GET'])]
    public function index(DomainsRepository $domainsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('domains/index.html.twig', [
            'domains' => $domainsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_domains_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DomainsRepository $domainsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $domain = new Domains();
        $form = $this->createForm(DomainsType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domainsRepository->add($domain, true);

            return $this->redirectToRoute('app_domains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('domains/new.html.twig', [
            'domain' => $domain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_domains_show', methods: ['GET'])]
    public function show(Domains $domain): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('domains/show.html.twig', [
            'domain' => $domain,
	    'techs' => $domain->getTechsCounter() .': '. implode( ', ', $domain->getTechnologies()->getValues()),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_domains_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Domains $domain, DomainsRepository $domainsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(DomainsType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domainsRepository->add($domain, true);

            return $this->redirectToRoute('app_domains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('domains/edit.html.twig', [
            'domain' => $domain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_domains_delete', methods: ['POST'])]
    public function delete(Request $request, Domains $domain, DomainsRepository $domainsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$domain->getId(), strval($request->request->get('_token')))) {
            $domainsRepository->remove($domain, true);
        }

        return $this->redirectToRoute('app_domains_index', [], Response::HTTP_SEE_OTHER);
    }
}
