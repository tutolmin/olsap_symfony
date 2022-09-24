<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    /**
     * @Route("/lucky/number")
     */
    public function number(): Response
    {
        $this->logger->debug(__METHOD__);

        $number = random_int(0, 100);
/*
        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
 */
        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}
