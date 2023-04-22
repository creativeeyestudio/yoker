<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAdminFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/admin/users/modify/{id}', name: 'admin_users_modify')]
    public function modifyUser(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $encoder, String $id){
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        $userRole = $user->getRoles()[0];
        $form = $this->createForm(UserAdminFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = explode(',', $form->get('roles')->getData());
            $user->setRoles($newRole);
            if ($form->get('remake_pass')->getData() != null) {
                $new_pwd = 'changePassword!!!';
                $password = $encoder->hashPassword($user, $new_pwd);
                $user->setPassword($password);
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users_modify',  ['id' => $user->getId()] );
        }

        return $this->render('admin_users/user-manager.html.twig', [
            'form' => $form,
            'userRole' => $userRole
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
