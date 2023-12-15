<?php

namespace App\Controller;

use App\Entity\PagesList;
use App\Entity\PostsList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $limit = 5;

        return $this->render('admin/index.html.twig', [
            'phpversion' => phpversion(),
            'symfonyversion' => Kernel::VERSION,
            'pagesList' => $em->getRepository(PagesList::class)->findBy([], ['id' => 'DESC'], $limit),
            'postsList' => $em->getRepository(PostsList::class)->findBy([], ['id' => 'DESC'], $limit),
            'usersList' => $em->getRepository(User::class)->findBy([], ['id' => 'DESC'], $limit),
        ]);
    }
}
