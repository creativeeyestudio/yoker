<?php

namespace App\Services;

use App\Entity\Comments;
use App\Entity\GlobalSettings;
use App\Entity\Menu;
use App\Entity\PagesList;
use App\Entity\PostsList;
use App\Entity\SocialManager;
use App\Form\CommentsFormType;
use App\Form\PagesAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Locales;

class PagesService extends AbstractController
{

    private $em;
    private $settings;
    private $menus;
    private $pages_repo;
    private $posts_services;
    private $social;
    private $file_path;
    private $css_file;
    private $js_file;
    private $params;
    private $request;

    function __construct(EntityManagerInterface $em, PostsService $posts_services, ParameterBagInterface $params, RequestStack $request)
    {
        $this->em = $em;
        $this->pages_repo = $this->em->getRepository(PagesList::class);
        $this->menus = $this->em->getRepository(Menu::class);
        $this->settings = $this->em->getRepository(GlobalSettings::class)->find(1);
        $this->social = $this->em->getRepository(SocialManager::class)->find(1);
        $this->params = $params;
        $this->file_path = "/build/";
        $this->css_file = $this->file_path . $this->params->get('css_js_path') . ".css";
        $this->js_file = $this->file_path . $this->params->get('css_js_path') . ".js";
        $this->posts_services = $posts_services;
        $this->request = $request->getCurrentRequest();
    }

    #region Page Manager
    function PageManager(bool $newPage, string $page_id = null)
    {
        // CREATION / RECUPERATION D'UNE PAGE
        // --------------------------------------------------------
        $page = $newPage ? new PagesList() : $this->pages_repo->findOneBy(['page_id' => $page_id]);
        if (!$page) {
            throw $this->createNotFoundException("Aucune page n'a été trouvée");
        }

        // INITIALISATION DU FORMULAIRE
        // --------------------------------------------------------
        $form = $this->createForm(PagesAdminFormType::class, $page);
        $form->handleRequest($this->request);

        // ENVOI DU FORMULAIRE
        // --------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $page = $form->getData();

            // Création du nom
            $name = [$form->get('page_name_fr')->getData()];
            $page->setPageName($name);

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($name[0]);
            $slugUrl = $slugify->slugify($form->get('page_url')->getData() ?? $slugName);

            // Création de l'ID Page
            if ($newPage) {
                $page->setPageId($slugName);
            }

            // Page principale
            if ($form->get('main_page')->getData()) {
                $main_page = $this->pages_repo->findOneBy(['main_page' => 1]);
                if ($main_page) $main_page->setMainPage(0);
                $page->setMainPage(1);
            }

            // Création / Modification de l'URL
            $page->setPageUrl(empty($form->get('page_url')->getData()) ? ($newPage ? $slugName : $page->getPageUrl()) : $slugUrl);

            // Création / Modification du Meta Title
            $metaTitle = [$form->get('page_meta_title_fr')->getData() ?? $name[0]];
            $page->setPageMetaTitle($metaTitle);

            // Création / Modification du Meta Desc
            $metaDesc = [$form->get('page_meta_desc_fr')->getData() ?? ''];
            $page->setPageMetaDesc($metaDesc);

            // Création / Modification du contenu
            $pageContent = [htmlspecialchars($form->get('page_content_fr')->getData()) ?? "Contenu à ajouter"];
            $page->setPageContent($pageContent);

            // Envoi des données vers la BDD
            $this->em->persist($page);
            $this->em->flush();
        }

        return $form;
    }
    #endregion

    #region Affichage d'une page
    public function getPageStatus(string $page_id = null)
    {
        $page = $this->pages_repo->findOneBy(['page_url' => $page_id]) ?? $this->pages_repo->findOneBy(['main_page' => 1]);
        if ($page->isMainPage() && $page_id) {
            return $this->redirectToRoute('web_index');
        }

        $lang = $this->lang_web($this->request);

        return $this->render('web_pages_views/index.html.twig', [
            'page_content' => htmlspecialchars_decode($page->getPageContent()[$lang]),
            'lang' => $lang,
            'lang_page' => $this->locales_web()[$lang],
            'meta_title' => $page->getPageMetaTitle()[$lang],
            'meta_desc' => $page->getPageMetaDesc()[$lang],
            'last_posts' => $this->posts_services->getLastPosts(),
            'posts' => $this->posts_services->getAllPosts(),
            'settings' => $this->settings,
            'menus' => $this->menus,
            'social' => $this->social,
            'css' => $this->css_file,
            'js' => $this->js_file,
        ]);
    }
    #endregion

    #region Affichage d'un post
    public function getPost(string $post_id)
    {
        // Création d'une nouvelle instance de commentaire
        $newComment = new Comments();

        // Récupération de l'article basé sur l'identifiant fourni
        $post = $this->em->getRepository(PostsList::class)->findOneBy(['post_url' => $post_id]);

        // Récupération des commentaires associés à l'article
        $comments = $this->em->getRepository(Comments::class)->findBy(['post' => $post], ['id' => 'DESC']);

        // Détection de la langue à partir de la requête HTTP
        $lang = $this->lang_web($this->request);

        // Vérification de la disponibilité de l'article
        if (!$post || !$post->isOnline()) {
            // Lancer une exception si l'article n'est pas disponible
            throw $this->createNotFoundException("Cet article n'est pas disponible");
        }

        // Création du formulaire de commentaire
        $form = $this->createForm(CommentsFormType::class, $newComment);
        $form->handleRequest($this->request);

        // Traitement du formulaire s'il a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le commentaire à l'article, le persister et le sauvegarder
            $newComment->setPost($post);
            $this->em->persist($newComment);
            $this->em->flush();

            // Retourner la vue après le traitement du formulaire
            return $this->redirectToRoute('web_post', [
                '_locale' => 'fr',
                'post_slug' => $post->getPostUrl()
            ]);
        }

        // Retourner la vue (également utilisé en cas de non soumission du formulaire)
        return $this->render('web_pages_views/post.html.twig', [
            'post_name' => $post->getPostName()[$lang],
            'post_slug' => $post->getPostUrl(),
            'post_thumb' => $post->getPostThumb(),
            'post_content' => htmlspecialchars_decode($post->getPostContent()[$lang]),
            'meta_title' => $post->getPostMetaTitle()[$lang],
            'meta_desc' => $post->getPostMetaDesc()[$lang],
            'social' => $this->social,
            'settings' => $this->settings,
            'menus' => $this->menus,
            'lang' => $this->lang_web($this->request),
            'lang_page' => $this->locales_web()[$lang],
            'comments' => $comments,
            'form' => $form->createView(),
            'css' => $this->css_file,
            'js' => $this->js_file,
        ]);
    }
    #endregion

    #region Sélection de la langue
    private function locales_web()
    {
        $locales = Locales::getLocales();
        $localesSite = [
            $locales[281], // FR
            // $locales[96] // EN
        ];
        return $localesSite;
    }

    private function lang_web()
    {
        $lang = array_search(
            $this->request->getLocale(),
            $this->locales_web()
        );
        return $lang;
    }
    #endregion   
}
