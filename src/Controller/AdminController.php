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
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController
{
    private $security;

    function __construct(Security $security)
    {
        $this->security = $security;
    }


    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $pagesList = $em->getRepository(PagesList::class)->findBy([], ['id' => 'DESC'], 5);
        $postsList = $em->getRepository(PostsList::class)->findBy([], ['id' => 'DESC'], 5);
        $usersList = $em->getRepository(User::class)->findBy([], ['id' => 'DESC'], 5);

        $user = $this->security->getUser();

        if (!$user->getIsVerified()) {
            throw $this->createNotFoundException("Votre compte n'est pas activé ! Cliquez sur le lien envoyé par mail ou contacter le gestionnaire du site pour une assistance");
        }

        return $this->render('admin/index.html.twig', [
            'phpversion' => phpversion(),
            'symfonyversion' => Kernel::VERSION,
            'pagesList' => $pagesList,
            'postsList' => $postsList,
            'usersList' => $usersList,
        ]);
    }
}
