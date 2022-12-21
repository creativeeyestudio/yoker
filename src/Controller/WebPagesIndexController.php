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
        $page = $doctrine->getRepository(PagesList::class)->findOneBy(["page_url" => "index"]);

        if (!$page) {
            throw $this->createNotFoundException(
                'La page d\'accueil du site est introuvable. Contactez le webmaster du site pour remédier au problème.'
            );
        }

        return $this->render('web_pages_index/index.html.twig', [
            'meta_title' => $page->getPageMetaTitle(),
            'meta_desc' => $page->getPageMetaDesc(),
        ]);
    }

    #[Route('/fr/index', name: 'web_index_redirect')]
    public function redirectIndex(){
        return $this->redirectToRoute('web_index');
    }
}
