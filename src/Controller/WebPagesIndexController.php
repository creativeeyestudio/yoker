<?php

namespace App\Controller;

use App\Entity\PagesList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WebPagesIndexController extends AbstractController
{

    // Page Generator
    // -----------------------------------------------------------------------------------------------------------------
    private function showPage(ManagerRegistry $doctrine, Request $request, string $page_id){
        $index_page = $doctrine->getRepository(PagesList::class)->findOneBy(["page_url" => $page_id]);
        
        if ($index_page){
            $page_lang = $request->getLocale();
            $meta_title = $index_page->getPageMetaTitle();
            $meta_desc = $index_page->getPageMetaDesc();
        } else {
            return $this->redirectToRoute('web_index');
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
    #[Route('/{_locale}', name: 'web_index', requirements: ['_locale' => 'fr|en'])]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $page = $this->showPage($doctrine, $request, 'index');
        return $page;
    }


    // Other Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/{page_slug}', name: 'web_page', requirements: ['_locale' => 'fr|en'])]
    public function page(ManagerRegistry $doctrine, Request $request, string $page_slug): Response
    {
        $page = $this->showPage($doctrine, $request, $page_slug);
        if($page_slug == 'index')
            return $this->redirectBase();
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
