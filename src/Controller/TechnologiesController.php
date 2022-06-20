<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Technologies;
use App\Repository\TechnologiesRepository;

class TechnologiesController extends AbstractController
{
    #[Route('/technologies', name: 'app_technologies')]
    public function index(): Response
    {
        return $this->render('technologies/index.html.twig', [
            'controller_name' => 'TechnologiesController',
        ]);
    }

    /**
     * @Route("/technologies/{id}", name="technologies_show", requirements={"id"="\d+"})
     */
    public function show(Technologies $technology): Response
    {
        // use the Product!
            // ...
            //
	$domain = $technology->getDomain();

        return $this->render('technologies/show.html.twig', [
#            'category' => '...',
#            'promotions' => ['...', '...'],
	    'domain' => $domain,
            'technology' => $technology,
        ]);

#        return new Response('Check out this great Technologies: '.$technology->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }

}
