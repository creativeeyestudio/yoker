<?php

namespace App\Controller;

use App\Entity\PagesList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WebPagesIndexController extends AbstractController
{
    // Page Generator
    // -----------------------------------------------------------------------------------------------------------------
    private function showPage(ManagerRegistry $doctrine, int $lang, string $page_id){
        $index_page = $doctrine->getRepository(PagesList::class)->findOneBy(["page_url" => $page_id]);

        switch ($lang) {
            case 0:
                if ($index_page){
                    $page_lang = 'fr';
                    $meta_title = $index_page->getPageMetaTitle();
                    $meta_desc = $index_page->getPageMetaDesc();
                } else {
                    return $this->redirectToRoute('web_index');
                }
                break;
            case 1:
                if ($index_page){
                    $page_lang = 'en';
                    $meta_title = $index_page->getPageMetaTitleEn();
                    $meta_desc = $index_page->getPageMetaDescEn();
                } else {
                    return $this->redirectToRoute('web_index_en');
                }
                break;
            default:
                break;
        }

        return $this->render('web_pages_views/index.html.twig', [
            'page_id' => $index_page->getPageId(),
            'page_slug' => $index_page->getPageUrl(),
            'page_lang' => $page_lang,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
        ]);
    }

    // Index Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/fr', name: 'web_index')]
    public function index_fr(ManagerRegistry $doctrine): Response
    {
        $page = $this->showPage($doctrine, 0, 'index');
        return $page;
    }

    #[Route('/en', name: 'web_index_en')]
    public function index_en(ManagerRegistry $doctrine): Response
    {
        $page = $this->showPage($doctrine, 1, 'index');
        return $page;
    }


    // Other Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/fr/{page_slug}', name: 'web_page')]
    public function page_fr(ManagerRegistry $doctrine, string $page_slug): Response
    {
        $page = $this->showPage($doctrine, 0, $page_slug);
        if($page_slug == 'index')
            return $this->redirectToRoute('web_index');
        else
            return $page;
    }

    #[Route('/en/{page_slug}', name: 'web_page_en')]
    public function page_en(ManagerRegistry $doctrine, string $page_slug): Response
    {
        $page = $this->showPage($doctrine, 1, $page_slug);
        if($page_slug == 'index')
            return $this->redirectToRoute('web_index_en');
        else
            return $page;
    }

    // Redirections
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/', name: 'web_redirect')]
    public function redirectBase(){
        return $this->redirectToRoute('web_index');
    }
}
