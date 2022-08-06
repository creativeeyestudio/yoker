<?php

namespace App\Controller;

use App\Entity\PostsList;
use App\Form\PagesAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostsController extends AbstractController
{
    #[Route('/admin/posts', name: 'app_admin_posts')]
    public function index(): Response
    {
        return $this->render('posts/index.html.twig', [
            'controller_name' => 'AdminPostsController',
        ]);
    }

    #[Route('/admin/posts/ajouter', name: 'admin_posts_add')]
    public function add_page(ManagerRegistry $doctrine, Request $request) {
        $form = $this->createForm(PagesAdminFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $slugify = new Slugify();
            $data = $form->getData();
            $slugify = new Slugify();
            $data = $form->getData();
            $postName = $data['page_name'];
            $postUrl = $data['page_url'];
            $postId = $slugify->slugify($postName);
            $postContent = $data['page_content'];
            $postMetaTitle = $data['page_meta_title'];
            $postMetaDesc = $data['page_meta_desc'];
            $postFileName = $postId . ".html.twig";

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $post = new PostsList();
            $post->setPostName($postName);
            $post->setPostId($postId);

            if ($postUrl != null) {
                $post->setPostUrl($postUrl);
            } else {
                $post->setPostUrl($postId);
            }

            if ($postMetaTitle != null) {
                $post->setPostMetaTitle($postMetaTitle);
            } else {
                $post->setPostMetaTitle($postName);
            }

            $post->setPostMetaDesc($postMetaDesc);
            $entityManager->persist($post);
            $entityManager->flush();

            // Création du fichier
            $file = fopen("../templates/webpages/posts/" . $postFileName, 'w');
            fwrite($file, $postContent);
            fclose($file);

            // Redirection vers le post crée
            return $this->redirectToRoute('admin_posts_modify', array('post_id' => $postId));
        }

        return $this->render('posts/add-post.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }

    /* MODIFIER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/posts/modifier/{post_id}', name: 'admin_posts_modify')]
    public function modify_page() {
        $form = $this->createForm(PagesAdminFormType::class);
        
        return $this->render('posts/modify-post.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }
}
