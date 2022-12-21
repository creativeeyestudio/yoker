<?php

namespace App\Controller;

use App\Entity\PagesList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebPagesOthersController extends AbstractController
{
    #[Route('/fr/{page_slug}', name: 'web_page')]
    public function index(ManagerRegistry $doctrine, string $page_slug): Response
    {
        $page = $doctrine->getRepository(PagesList::class)->findOneBy(["page_url" => $page_slug]);

        if (!$page) {
            throw $this->createNotFoundException(
                'La page demandée est introuvable. Contactez le webmaster du site pour remédier au problème.'
            );
        }

        return $this->render('web_pages_others/index.html.twig', [
            'page_id' => $page->getPageId(),
            'meta_title' => $page->getPageMetaTitle(),
            'meta_desc' => $page->getPageMetaDesc(),
        ]);
    }
}
