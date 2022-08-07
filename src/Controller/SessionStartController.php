<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionStartController extends AbstractController
{
    #[Route('/session/start', name: 'app_session_start')]
    public function index(): Response
    {
        return $this->render('session_start/index.html.twig', [
            'controller_name' => 'SessionStartController',
        ]);
    }
}
