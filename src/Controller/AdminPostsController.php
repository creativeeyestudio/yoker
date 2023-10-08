<?php

namespace App\Controller;

use App\Entity\MenuLink;
use App\Entity\PostsList;
use App\Services\PostsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostsController extends AbstractController
{
    private $em;
    private $postService;
    private $security;
    private $postsRepo;
    private $menusRepo;

    public function __construct(EntityManagerInterface $em, PostsService $postService, Security $security) {
        $this->em = $em;
        $this->postService = $postService;
        $this->security = $security;
        $this->postsRepo = $this->em->getRepository(PostsList::class);
        $this->menusRepo = $this->em->getRepository(MenuLink::class);
    }

    /* LISTE DES POSTS
    ------------------------------------------------------- */
    #[Route('/admin/posts', name: 'admin_posts')]
    public function index(): Response
    {
        return $this->render('posts/index.html.twig', [
            "posts" => $this->postsRepo->findAll()
        ]);
    }


    /* AJOUTER UN POST
    ------------------------------------------------------- */
    #[Route('/admin/posts/ajouter', name: 'admin_posts_add')]
    public function add_post(Request $request) 
    {
        $form = $this->postService->PostManager($request, $this->security, true);

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
    public function modify_post(Request $request, String $post_id)
    {
        // Récupération du contenu de la page
        $post = $this->postsRepo->find($post_id);

        // Initialisation du formulaire
        $form = $this->postService->PostManager($request, $this->security, false, $post_id);

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
    public function delete_post(string $post_id)
    {
        $post = $this->postsRepo->find($post_id);
        $menuLink = $this->menusRepo->findBy(['post' => $post]);

        if ($post) {
            if ($menuLink) {
                foreach($menuLink as $link){
                    $this->em->remove($link);
                }
            }
            $this->em->remove($post);
            $this->em->flush();
        } else {
            throw $this->createNotFoundException("Aucun post n'a été trouvé");
        }

        return $this->redirectToRoute('admin_posts');
    }
}
