<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Testees;
use App\Form\TesteesType;
use App\Repository\TesteesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class IndexController extends AbstractController
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private $testeesRepository;
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
        $this->entityManager = $entityManager;
        $this->testeesRepository = $this->entityManager->getRepository( Testees::class);
    }

    #[Route('/', name: 'app_testees_display', methods: ['GET'])]
    public function display(): Response {
        $this->logger->debug(__METHOD__);

        // TODO: get testee id by oauth token
        // Check if the Environment exists
        $testee = $this->testeesRepository->find(1);

//        $sessions = array();
        $session_links = array();
        $session_names = array();

        foreach ($testee->getSessions()->getValues() as $s) {
//            $sessions[] = $s;
            $session_links[$s->getHash()] = $this->generateUrl('app_sessions_display', 
                    ['hash' => $s->getHash()]);
            $session_names[$s->getHash()] = $s;
        }

        return $this->render('testees/display.html.twig', [
                    'testee' => $testee,
//                    'sessions' => $testee->getSessionsCounter() . ': ' . implode(', ', $sessions),
                    'session_links' => $session_links,
                    'session_names' => $session_names,
        ]);
    }
}
