<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\AwxEvent;

class AwxProjectController extends AbstractController
{
    private LoggerInterface $logger;
    
    /**
     * 
     * @var MessageBusInterface
     */
    private $awxEventBus;
    
    // Dependency injection of the LoggerInterface entity
    public function __construct( LoggerInterface $logger, MessageBusInterface $awxEventBus)
    {   
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
        $this->awxEventBus = $awxEventBus;
    }

    #[Route('/awx/project', name: 'app_awx_project', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $this->logger->debug($request->getPathInfo());
        $this->logger->debug($request);
//        $this->logger->debug($request->getPayload());
//        $this->logger->debug(json_decode($request->getContent(), true));

        // Dispatch AWX event message
	$this->awxEventBus->dispatch(new AwxEvent(["event" => "project", 
            "id" => (int)$request->getPayload()->get('id')]));
        
        return $this->render('awx_web_hook/index.html.twig', [
            'controller_name' => 'AwxProjectController',
        ]);
    }
}
