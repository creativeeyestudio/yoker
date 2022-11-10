<?php

namespace App\Controller;

use App\Entity\PostsList;
use App\Form\PagesAdminFormType;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostsController extends AbstractController
{
    #[Route('/admin/posts', name: 'app_admin_posts')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $postsRepo = $entityManager->getRepository(PostsList::class);
        $posts = $postsRepo->findAll();

        return $this->render('posts/index.html.twig', [
            'controller_name' => 'AdminPostsController',
            "posts" => $posts
        ]);
    }

    /* AJOUTER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/posts/ajouter', name: 'admin_posts_add')]
    public function add_post(ManagerRegistry $doctrine, Request $request) {

        $post = new PostsList();
        $form = $this->createForm(PostsAdminFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $post = $form->getData();

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($form->get('post_name')->getData());
            $slugUrl = $slugify->slugify($form->get('post_url')->getData());

            // Création de l'ID Post
            $post->setPostId($slugName);

            // Création de l'URL
            if (!$form->get('post_meta_title')->getData()) {
                $post->setPostUrl($slugName);
            } else {
                $post->setPostUrl($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('post_meta_title')->getData()) {
                $post->setPostMetaTitle($form->get('post_name')->getData());
            } else {
                $post->setPostMetaTitle($form->get('post_meta_title')->getData());
            }

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            // Création du fichier TWIG
            $file = fopen("../templates/webpages/posts/" . $slugName . '.html.twig', 'w');
            fwrite($file, $form->get('post_content')->getData());
            fclose($file);

            return $this->redirectToRoute('app_admin_posts');
        }

        return $this->render('posts/add-post.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdminPostsController',
        ]);
    }

    /* MODIFIER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/posts/modifier/{post_id}', name: 'admin_posts_modify')]
    public function modify_post(ManagerRegistry $doctrine, Request $request, String $post_id) {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(PostsList::class)->findOneBy(['post_id' => $post_id]);
        $form = $this->createForm(PostsAdminFormType::class, $post);
        $form->handleRequest($request);

        // Récupération du post souhaité
        if(!$post) {
            throw $this->createNotFoundException(
                "Aucune page n'a été trouvée"
            );
        }

        // Récupération du contenu de la page
        $postContent = file_get_contents("../templates/webpages/posts/" . $post_id . ".html.twig");
        
        if ($form->isSubmitted() && $form->isValid()) { 
            // Récupération des données du formulaire
            $post = $form->getData();
            $postId = $post->getPostId();
            $postFileName = $postId . ".html.twig";

            // Création du slug
            $slugify = new Slugify();
            $slugUrl = $slugify->slugify($form->get('post_url')->getData());

            // Création de l'URL
            if (!$form->get('post_meta_title')->getData()) {
                $post->setPostUrl($post->getPostUrl());
            } else {
                $post->setPostUrl($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('post_meta_title')->getData()) {
                $post->setPostMetaTitle($form->get('post_name')->getData());
            } else {
                $post->setPostMetaTitle($form->get('post_meta_title')->getData());
            }

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            
             // Modification du contenu de la page
             unlink("../templates/webpages/posts/" . $postFileName);
             $file = fopen("../templates/webpages/posts/" . $postFileName, 'w');
             fwrite($file, $form->get('post_content')->getData());
             fclose($file);

             // Redirection vers la page crée
             return $this->redirectToRoute('admin_posts_modify', array('post_id' => $postId));
        }
        
        return $this->render('posts/modify-post.html.twig', [
            'form' => $form->createView(),
            'content' => $postContent,
        ]);
    }

    /* SUPPRIMER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/post/supprimer/{post_id}', name: 'admin_posts_delete')]
    public function delete_post(ManagerRegistry $doctrine, String $post_id)
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(PostsList::class)->findOneBy(['post_id' => $post_id]);

        if(!$post) {
            throw $this->createNotFoundException(
                "Aucune post n'a été trouvé"
            );
        }

        $entityManager->remove($post);
        $entityManager->flush();

        // Suppression du fichier
        unlink("../templates/webpages/posts/" . $post_id . ".html.twig");

        return $this->redirectToRoute('app_admin_posts');
    }
}
