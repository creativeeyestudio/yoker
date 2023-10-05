<?php

namespace App\Services;

use App\Entity\PostsList;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class PostsService extends AbstractController{

    private $doctrine;
    private $posts_repo;
    private $em;

    function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->posts_repo = $this->em->getRepository(PostsList::class);
    }

    #region Création / Modification d'un post
    function PostManager(Request $request, Security $security, bool $newPost, string $postId = null)
    {
        $slugify = new Slugify();

        // CREATION / RECUPERATION D'UN POST
        // --------------------------------------------------------
        $post = ($newPost) ? new PostsList() : $this->posts_repo->find($postId);
        if (!$post) {
            throw $this->createNotFoundException("Aucune post n'a été trouvé");
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
            $metaTitle = [
                !($form->get('post_meta_title_fr')->getData()) ? $name[0] : $form->get('post_meta_title_fr')->getData()
            ];
            $post->setPostMetaTitle($metaTitle);

            // Création / Modification du Meta Desc
            $metaDesc = [
                $form->get('post_meta_desc_fr')->getData()
            ];
            $post->setPostMetaDesc($metaDesc);

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
            $this->em->persist($post);
            $this->em->flush();
        }

        return $form;
    }
    #endregion

    #region Affichage des posts
    public function getAllPosts(){
        $posts = $this->posts_repo->findAll();
        $post_array = [];
        foreach ($posts as $post) {
            $last_post_array[] = [
                'id' => $post->getId(),
                'name' => $post->getPostName(),
                'url' => $post->getPostUrl(),
                'date' => $post->getCreatedAt(),
            ];
        };
        return $post_array;
    }
    #endregion

    #region Affichage des derniers posts
    public function getLastPosts()
    {
        $last_posts = $this->posts_repo->findBy([], ['created_at' => 'DESC'], 3);
        $last_post_array = [];
        foreach ($last_posts as $post) {
            $last_post_array[] = [
                'id' => $post->getId(),
                'name' => $post->getPostName(),
                'url' => $post->getPostUrl(),
                'date' => $post->getCreatedAt(),
            ];
        };
        return $last_post_array;
    }
    #endregion

    #region Affichage d'un post
    public function getPost(string $post_url){
        $post = $this->posts_repo->findOneBy(["post_url" => $post_url]);
    }
    #endregion
}