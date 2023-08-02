<?php

namespace App\Services;

use App\Entity\PostsList;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class PostsService extends AbstractController{

    function PostManager(ManagerRegistry $doctrine, Request $request, Security $security, bool $newPost, string $postId = null)
    {
        $em = $doctrine->getManager();
        $postRepo = $em->getRepository(PostsList::class);
        $slugify = new Slugify();

        // CREATION / RECUPERATION D'UN POST
        // --------------------------------------------------------
        if ($newPost) {
            $post = new PostsList();
        } else {
            $post = $postRepo->find($postId);
            if (!$post) {
                throw $this->createNotFoundException("Aucune post n'a été trouvé");
            }
        }

        // INITIALISATION DU FORMULAIRE
        // --------------------------------------------------------
        $form = $this->createForm(PostsAdminFormType::class, $post);
        $form->handleRequest($request);

        // ENVOI DU FORMULAIRE
        // --------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $post = $form->getData();

            // Création / Modification du nom
            $name = [$form->get('post_name_fr')->getData()];
            $post->setPostName($name);

            // Création / Modification du contenu
            $content = [htmlspecialchars($form->get('post_content_fr')->getData())];
            $post->setPostContent($content);

            // Création / Modification du Meta Title
            $metaTitleFr = $form->get('post_meta_title_fr')->getData();
            $post->setPostMetaTitle([
                !($metaTitleFr) ? $name[0] : $metaTitleFr
            ]);

            // Création / Modification du Meta Desc
            $metaDescFr = $form->get('post_meta_desc_fr')->getData();
            $post->setPostMetaDesc([$metaDescFr]);

            // Création de l'URL
            if ($newPost) {
                $slugName = $slugify->slugify($name[0]);
                if ($slugName) {
                    $post->setPostUrl($slugName);
                }
            }

            // Gestion des dates
            $currentDate = new DateTimeImmutable();
            $post->setUpdatedAt($currentDate);
            if ($newPost) {
                $post->setCreatedAt($currentDate);
            }

            // Création de l'auteur
            if ($newPost) {
                $author = $security->getUser();
                $post->setAuthor($author);
            }

            // Création de l'image
            $imageFile = $form->get('post_thumb')->getData();
            if ($imageFile) {
                $imageName = $slugify->slugify($imageFile->getClientOriginalName());
                $ext = $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('posts_img_directory'),
                        $imageName
                    );
                    $post->setPostThumb($imageName);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            
            // Envoi des données vers la BDD
            $em->persist($post);
            $em->flush();
        }

        return $form;
    }
}