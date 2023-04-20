<?php

namespace App\Controller;

use App\Entity\PagesList;
use App\Entity\PostsList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WebPagesIndexController extends AbstractController
{
    #region Page

    // Page Generator
    // -----------------------------------------------------------------------------------------------------------------
    private function showPage(ManagerRegistry $doctrine, Request $request, string $page_id){
        $page = $doctrine->getRepository(PagesList::class)->findOneBy(["page_url" => $page_id]);
        $posts = $doctrine->getRepository(PostsList::class)->findAll();
        
        if ($page){
            $page_lang = $request->getLocale();
            $meta_title = $page->getPageMetaTitle();
            $meta_desc = $page->getPageMetaDesc();
        } else {
            return $this->redirectToRoute('web_index');
        }

        return $this->render('web_pages_views/index.html.twig', [
            'page_id' => $page->getPageId(),
            'page_slug' => $page->getPageUrl(),
            'page_lang' => $page_lang,
            'posts' => $posts,
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

    #endregion

    #region Post

    // Post Generator
    // -----------------------------------------------------------------------------------------------------------------
    public function showPost(ManagerRegistry $doctrine, Request $request, string $post_url){
        $post = $doctrine->getRepository(PostsList::class)->findOneBy(["post_url" => $post_url]);
        $post_lang = $request->getLocale();
        $meta_title = $post->getPostMetaTitle();
        $meta_desc = $post->getPostMetaDesc();

        return $this->render('web_pages_views/post.html.twig', [
            'post_id' => $post->getPostId(),
            'post_slug' => $post->getPostUrl(),
            'post_thumb' => $post->getPostThumb(),
            'post_lang' => $post_lang,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
        ]);
    }

    // Post Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/blog/{post_url}', name: 'web_post', requirements: ['_locale' => 'fr|en'])]
    public function post(ManagerRegistry $doctrine, Request $request, string $post_url): Response
    {
        $post = $this->showPost($doctrine, $request, $post_url);
        return $post;
    }

    #endregion
}
