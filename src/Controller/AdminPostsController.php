<?php

namespace App\Controller;

use App\Form\PagesAdminFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function add_page() {
        $form = $this->createForm(PagesAdminFormType::class);

        return $this->render('posts/add-post.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/admin/posts/modifier', name: 'admin_posts_modify')]
    public function modify_page() {
        $form = $this->createForm(PagesAdminFormType::class);
        
        return $this->render('posts/modify-post.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }
}
