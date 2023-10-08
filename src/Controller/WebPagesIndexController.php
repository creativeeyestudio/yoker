<?php

namespace App\Controller;

use App\Services\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebPagesIndexController extends AbstractController
{
    private $pages_services;

    public function __construct(PagesService $pages_services)
    {
        $this->pages_services = $pages_services;
    }
    
    // Index Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}', name: 'web_index', requirements: ['_locale' => 'fr'])]
    public function index(Request $request): Response
    {
        return $this->pages_services->getMainPage($request);
    }

    // Other Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/{page_slug}', name: 'web_page', requirements: ['_locale' => 'fr'])]
    public function page(Request $request, string $page_slug): Response
    {
        return $this->pages_services->getPage($request, $page_slug);
    }
    
    // Post Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/blog/{post_url}', name: 'web_post', requirements: ['_locale' => 'fr|en'])]
    public function post(string $post_url, Request $request): Response
    {
        return $this->pages_services->getPost($request, $post_url);
    }

    // Redirections
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/', name: 'web_redirect')]
    public function redirectBase(){
        return $this->redirectToRoute('web_index');
    }
}
