<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AwxWebHookController extends AbstractController
{
    private $logger;
    
    // Dependency injection of the LoggerInterface entity
    public function __construct( LoggerInterface $logger)
    {   
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/awx/web/hook', name: 'app_awx_web_hook', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $this->logger->debug($request->getPathInfo());
        $this->logger->debug($request);
//        $this->logger->debug($request->getPayload());
//        $this->logger->debug(json_decode($request->getContent(), true));

        return $this->render('awx_web_hook/index.html.twig', [
            'controller_name' => 'AwxWebHookController',
        ]);
    }
}
