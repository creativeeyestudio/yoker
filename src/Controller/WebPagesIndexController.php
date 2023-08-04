<?php

namespace App\Controller;

use App\Entity\GlobalSettings;
use App\Entity\Menu;
use App\Entity\PagesList;
use App\Entity\PostsList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Routing\Annotation\Route;


class WebPagesIndexController extends AbstractController
{
    #region Page
    // Page Generator
    // -----------------------------------------------------------------------------------------------------------------
    private function showPage(ManagerRegistry $doctrine, Request $request, string $page_id): Response
    {
        $page_base = $doctrine->getRepository(PagesList::class);
        $page = $page_base->findOneBy(['page_url' => $page_id]);
        $post_base = $doctrine->getRepository(PostsList::class);
        $posts = $post_base->findAll();
        $menus = $doctrine->getRepository(Menu::class);

        $lang = $request->getLocale();
        $locales = Locales::getLocales();
        $localesSite = [
            $locales[280], // FR
            $locales[96] // EN
        ];

        $lang = array_search($lang, $localesSite);
        $meta_title = $page->getPageMetaTitle()[$lang];
        $meta_desc = $page->getPageMetaDesc()[$lang];
        $page_content = $page->getPageContent()[$lang];

        if (!$page || !$page->isStatus()) {
            return (!$page) ? $this->redirectToRoute('web_index') : throw $this->createNotFoundException("Cette page n'est pas disponible");
        }

        $settings = $doctrine->getRepository(GlobalSettings::class)->findOneBy(['id' => 0]);

        return $this->render('web_pages_views/index.html.twig', [
            'page_content' => htmlspecialchars_decode($page_content),
            'posts' => $posts,
            'lang' => $lang,
            'lang_page' => $localesSite[$lang],
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'settings' => $settings,
            'menus' => $menus,
        ]);
    }

    // Index Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}', name: 'web_index', requirements: ['_locale' => 'fr'])]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        return $this->showPage($doctrine, $request, 'index');
    }


    // Other Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/{page_slug}', name: 'web_page', requirements: ['_locale' => 'fr'])]
    public function page(ManagerRegistry $doctrine, Request $request, string $page_slug): Response
    {
        return ($page_slug === 'index') ? $this->redirectBase() : $this->showPage($doctrine, $request, $page_slug);
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
        $menus = $doctrine->getRepository(Menu::class);
        $statut = $post->isOnline();

        if (!$statut) {
            throw $this->createNotFoundException("Cet article n'est pas disponible");
        }

        $lang = $request->getLocale();
        $locales = Locales::getLocales();
        $localesSite = [
            $locales[280], // FR
            $locales[96] // EN
        ];
        
        $lang = array_search($lang, $localesSite);
        $meta_title = $post->getPostMetaTitle()[array_search($lang, $localesSite)];
        $meta_desc = $post->getPostMetaDesc()[array_search($lang, $localesSite)];
        $post_content = $post->getPostContent()[array_search($lang, $localesSite)];

        return $this->render('web_pages_views/post.html.twig', [
            'post_slug' => $post->getPostUrl(),
            'post_thumb' => $post->getPostThumb(),
            'post_content' => htmlspecialchars_decode($post_content),
            'menus' => $menus,
            'lang' => $lang,
            'lang_page' => $localesSite[$lang],
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
        ]);
    }

    // Post Page
    // -----------------------------------------------------------------------------------------------------------------
    #[Route('/{_locale}/blog/{post_url}', name: 'web_post', requirements: ['_locale' => 'fr|en'])]
    public function post(ManagerRegistry $doctrine, Request $request, string $post_url): Response
    {
        return $this->showPost($doctrine, $request, $post_url);
    }

    #endregion
}
