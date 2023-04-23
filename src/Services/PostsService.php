<?php

namespace App\Services;

use App\Entity\PostsList;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
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

            // Création des dates
            if ($newPost) {
                $post->setCreatedAt(new DateTimeImmutable());
                $post->setUpdatedAt(new DateTimeImmutable());
            } else {
                $post->setUpdatedAt(new DateTimeImmutable());
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

            // Création / Modification de l'image principale
            if ($form->get('post_thumb')->getData() != null) {
                $postImg = $form->get('post_thumb')->getData();
                $imgFile = md5(uniqid()) . '.' . $postImg->guessExtension();
                $postImg->move(
                    $this->getParameter('posts_img_directory'),
                    $imgFile
                );
                $post->setPostThumb($imgFile);
            }
            

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            // Création du fichier TWIG
            if ($newPost) {
                $file = fopen("../templates/webpages/posts/fr/" . $slugName . '.html.twig', 'w');
                $file_en = fopen("../templates/webpages/posts/en/" . $slugName . '.html.twig', 'w');
            } else {
                $postFileName = $post->getPostId() . '.html.twig';
                unlink("../templates/webpages/posts/fr/" . $postFileName);
                unlink("../templates/webpages/posts/en/" . $postFileName);
                $file = fopen("../templates/webpages/posts/fr/" . $postFileName, 'w');
                $file_en = fopen("../templates/webpages/posts/en/" . $postFileName, 'w');
            }
            fwrite($file, $form->get('post_content')->getData());
            fwrite($file_en, $form->get('post_content_en')->getData());
            fclose($file);
            fclose($file_en);
        }

        return $form;
    }

}