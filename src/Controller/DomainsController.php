<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Domains;
use App\Repository\DomainsRepository;

class DomainsController extends AbstractController
{
    #[Route('/domains', name: 'app_domains')]
    public function index(): Response
    {
        return $this->render('domains/index.html.twig', [
            'controller_name' => 'DomainsController',
        ]);
    }

    /**
     * @Route("/domains/{id}", name="domains_show", requirements={"id"="\d+"})
     */
    public function show(Domains $domain): Response
    {
        // use the Product!
	    // ...
	    //

        return new Response('Check out this great Domains: '.$domain->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);	
    }

    /**
     * @Route("/domains/list", name="domains_list")
     */
    public function list(DomainsRepository $domainsRepository): Response
    {
        $domains = $domainsRepository
            ->findAll();

        // the `render()` method returns a `Response` object with the
        // contents created by the template
        return $this->render('domains/list.html.twig', [
            'category' => '...',
            'promotions' => ['...', '...'],
	    'domains' => $domains,
        ]);
    }
}
