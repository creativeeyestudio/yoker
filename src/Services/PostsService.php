<?php

namespace App\Services;

use App\Entity\PostsList;
use App\Form\PostsAdminFormType;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PostsService extends AbstractController
{
    private $em;
    private $posts_repo;
    private $request;

    function __construct(EntityManagerInterface $em, RequestStack $request) {
        $this->em = $em;
        $this->posts_repo = $this->em->getRepository(PostsList::class);
        $this->request = $request->getCurrentRequest();
    }

    #region Création / Modification d'un post
    function PostManager(Security $security, bool $newPost, string $postId = null) : FormInterface
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
        $form->handleRequest($this->request);

        // ENVOI DU FORMULAIRE
        // --------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $post = $form->getData();

            // Création / Modification du nom
            $name = [$form->get('post_name_fr')->getData()];

            // Création / Modification du contenu
            $content = [htmlspecialchars($form->get('post_content_fr')->getData())];

            // Création / Modification du Meta Title
            $metaTitle = [
                !($form->get('post_meta_title_fr')->getData()) ? $name[0] : $form->get('post_meta_title_fr')->getData()
            ];
            // Création / Modification du Meta Desc
            $metaDesc = [
                $form->get('post_meta_desc_fr')->getData()
            ];

            // Création de l'URL
            if ($newPost) {
                $slugName = $slugify->slugify($name[0]);
                if ($slugName) {
                    $post->setPostUrl($slugName);
                }
            }

            // Gestion des dates
            $currentDate = new DateTimeImmutable();
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
            
            $post
                ->setPostName($name)
                ->setPostContent($content)
                ->setPostMetaTitle($metaTitle)
                ->setPostMetaDesc($metaDesc)
                ->setUpdatedAt($currentDate);
            
            // Envoi des données vers la BDD
            $this->em->persist($post);
            $this->em->flush();
        }

        return $form;
    }
    #endregion

    #region Affichage des posts
    public function getAllPosts() : array
    {
        $list = $this->posts_repo->findAll();
        return array_map(fn ($post) => $this->modelJSON($post), $list);
    }
      
    #endregion

    #region Affichage des derniers posts
    public function getLastPosts() : array
    {
        $list = $this->posts_repo->findBy([], ['created_at' => 'DESC'], 3);
        return array_map(fn ($post) => $this->modelJSON($post), $list);
    }
    
    #endregion

    #region Affichage d'un post
    public function getPost(string $post_url) : PostsList {
        return $this->posts_repo->findOneBy(["post_url" => $post_url]);
    }
    #endregion

    private function modelJSON($post) : array {
        return [
            'id' => $post->getId(),
            'name' => $post->getPostName(),
            'url' => $post->getPostUrl(),
            'date' => $post->getCreatedAt(),
        ];
    }
}