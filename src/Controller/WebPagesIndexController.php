<?php

namespace App\Controller;

use App\Services\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}', requirements: ['_locale' => LocaleConstraint::LOCALE_PATTERN])]
class WebPagesIndexController extends AbstractController
{
    private $pages_services;

    public function __construct(PagesService $pages_services)
    {
        $this->pages_services = $pages_services;
    }

    
    // Index Page
    // -------------------------------------------------------------------------------------------
    #[Route('/', name: 'web_index')]
    public function index(): Response
    {
        return $this->pages_services->getPageStatus();
    }


    // Other Page
    // -------------------------------------------------------------------------------------------
    #[Route('/{page_slug}', name: 'web_page')]
    public function page(string $page_slug): Response
    {
        return $this->pages_services->getPageStatus($page_slug);
    }


    // Post Page
    // -------------------------------------------------------------------------------------------
    #[Route('/blog/{post_slug}', name: 'web_post')]
    public function post(string $post_slug): Response
    {
        return $this->pages_services->getPost($post_slug);
    }
}

class LocaleConstraint
{
    const LOCALE_PATTERN = 'fr|en';
}
