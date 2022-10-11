<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsersController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin_users/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_users_delete')]
    public function deleteUser(ManagerRegistry $doctrine, String $id){
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([ 'id' => $id ]);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }
}
