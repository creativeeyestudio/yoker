<?php

namespace App\Controller;

use App\Entity\MenuLink;
use App\Entity\PostsList;
use App\Services\PostsService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostsController extends AbstractController
{

    /* LISTE DES POSTS
    ------------------------------------------------------- */
    #[Route('/admin/posts', name: 'admin_posts')]
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
    public function add_post(PostsService $postService, ManagerRegistry $doctrine, Request $request, Security $security) {

        $form = $postService->PostManager($doctrine, $request, $security, true);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('posts/post-manager.html.twig', [
            'form' => $form->createView(),
            'title' => "Ajouter un article",
        ]);
    }

    /* MODIFIER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/posts/modifier/{post_id}', name: 'admin_posts_modify')]
    public function modify_post(ManagerRegistry $doctrine, Request $request, String $post_id, PostsService $postService, Security $security) {

        // Récupération du contenu de la page
        $em = $doctrine->getManager();
        $post = $em->getRepository(PostsList::class)->find($post_id);
        $link = $post->getPostUrl();

        $form = $postService->PostManager($doctrine, $request, $security, false, $post_id);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_posts_modify', [
                'post_id' => $post_id
            ]);
        }
        
        return $this->render('posts/post-manager.html.twig', [
            'form' => $form->createView(),
            'title' => "Modifier un article",
            'postName_fr' => $post->getPostName()[0],
            'metaTitle_fr' => $post->getPostMetaTitle()[0],
            'metaDesc_fr' => $post->getPostMetaDesc()[0],
            'postContent_fr' => htmlspecialchars_decode($post->getPostContent()[0]),
        ]);
    }

    /* SUPPRIMER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/post/supprimer/{post_id}', name: 'admin_posts_delete')]
    public function delete_post(ManagerRegistry $doctrine, string $post_id)
    {
        $em = $doctrine->getManager();
        $post = $em->getRepository(PostsList::class)->find($post_id);
        $menuLink = $em->getRepository(MenuLink::class)->findBy(['post' => $post]);

        if ($post) {
            if ($menuLink) {
                foreach($menuLink as $link){
                    $em->remove($link);
                }
            }
            $em->remove($post);
            $em->flush();
        } else {
            throw $this->createNotFoundException("Aucun post n'a été trouvé");
        }

        return $this->redirectToRoute('admin_posts');
    }
}
