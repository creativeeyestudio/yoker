<?php

namespace App\Controller;

use App\Entity\PagesList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebPagesIndexController extends AbstractController
{
    #[Route('/fr', name: 'web_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $index_page = $doctrine->getRepository(PagesList::class)->findBy(["page_url" => "index"]);

        if (!$index_page) {
            throw $this->createNotFoundException(
                'La page d\'accueil du site est introuvable. Contactez le webmaster du site pour remédier au problème.'
            );
        }

        return $this->render('webpages/pages/accueil.html.twig', [
            'controller_name' => 'WebPagesIndexController',
        ]);
    }

    #[Route('/fr/index', name: 'web_index_redirect')]
    public function redirectIndex(){
        return $this->redirectToRoute('web_index');
    }

    #[Route('/', name: 'web_index_redirect')]
    public function redirectIndexBase(){
        return $this->redirectToRoute('web_index');
    }
}
