<?php

namespace App\Services;

use App\Entity\PostsList;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PostsService extends AbstractController{

    function PostManager(ManagerRegistry $doctrine, Request $request, bool $newPost, String $postId = null){

        // Initialisation du formulaire
        if ($newPost) {
            $post = new PostsList();
        } else {
            $entityManager = $doctrine->getManager();
            $post = $entityManager->getRepository(PostsList::class)->findOneBy(['post_id' => $postId]);
        }
        $form = $this->createForm(PostsAdminFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $post = $form->getData();
            if (!$newPost) {
                $postId = $post->getPostId();
                $postFileName = $postId . ".html.twig";
                // Récupération du post souhaité
                if(!$post) {
                    throw $this->createNotFoundException(
                        "Aucune page n'a été trouvée"
                    );
                }
            }

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($form->get('post_name')->getData());
            $slugUrl = $slugify->slugify($form->get('post_url')->getData());

            // Création de l'ID Post
            if ($newPost) {
                $post->setPostId($slugName);
            }

            // Création de l'URL
            if (!$form->get('post_url')->getData() && $newPost) {
                $post->setPostUrl($slugName);
            } elseif (!$form->get('post_url')->getData() && !$newPost) {
                $post->setPostUrl($post->getPostUrl());
            } else {
                $post->setPostUrl($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('post_meta_title')->getData() && $newPost) {
                $post->setPostMetaTitle($form->get('post_name')->getData());
            } elseif (!$form->get('post_meta_title')->getData() && !$newPost) {
                $post->setPostMetaTitle($post->getPostMetaTitle());
            } else {
                $post->setPostMetaTitle($form->get('post_meta_title')->getData());
            }

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            // Création du fichier TWIG
            if ($newPost) {
                $file = fopen("../templates/webpages/posts/" . $slugName . '.html.twig', 'w');
            } else {
                unlink("../templates/webpages/posts/" . $postFileName);
                $file = fopen("../templates/webpages/posts/" . $postFileName, 'w');
            }
            fwrite($file, $form->get('post_content')->getData());
            fclose($file);
        }

        return $form;
    }

}