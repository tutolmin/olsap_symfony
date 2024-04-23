<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use App\Entity\Breeds;
use App\Form\BreedsType;
use App\Repository\BreedsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreedsManager;

#[Route('/breeds')]
class BreedsController extends AbstractController
{
    private LoggerInterface $logger;
    private BreedsManager $breedsManager;
    
    public function __construct(LoggerInterface $logger,
            BreedsManager $breedsManager)
    {
        $this->logger = $logger;
        $this->breedsManager = $breedsManager;
        $this->logger->debug(__METHOD__);
    }

    #[Route('/', name: 'app_breeds_index', methods: ['GET'])]
    public function index(BreedsRepository $breedsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('breeds/index.html.twig', [
            'breeds' => $breedsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_breeds_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BreedsRepository $breedsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $breed = new Breeds();
        $form = $this->createForm(BreedsType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $breedsRepository->add($breed, true);

            return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('breeds/new.html.twig', [
            'breed' => $breed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_breeds_show', methods: ['GET'])]
    public function show(Breeds $breed): Response
    {
        $this->logger->debug(__METHOD__);

        return $this->render('breeds/show.html.twig', [
            'breed' => $breed,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_breeds_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Breeds $breed, BreedsRepository $breedsRepository): Response
    {
        $this->logger->debug(__METHOD__);

        $form = $this->createForm(BreedsType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $breedsRepository->add($breed, true);

            return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('breeds/edit.html.twig', [
            'breed' => $breed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_breeds_delete', methods: ['POST'])]
    public function delete(Request $request, Breeds $breed): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete'.$breed->getId(), 
                strval($request->request->get('_token')))) {
            $this->breedsManager->removeBreed($breed);
        }

        return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
    }
/*    
    #[Route('/{id}/delete_cascade', name: 'app_breeds_delete_cascade', methods: ['POST'])]
    public function delete_cascade(Request $request, Breeds $breed): Response
    {
        $this->logger->debug(__METHOD__);

        if ($this->isCsrfTokenValid('delete_cascade'.$breed->getId(), 
                strval($request->request->get('_token')))) {
            $this->breedsManager->removeBreed($breed, true);
        }

        return $this->redirectToRoute('app_breeds_index', [], Response::HTTP_SEE_OTHER);
    }
 * 
 */
}
