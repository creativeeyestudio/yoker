<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAdminFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsersController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin_users/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users/modify/{id}', name: 'admin_users_modify')]
    public function update(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $encoder, string $id): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        $userRole = $user->getRoles()[0];
        $form = $this->createForm(UserAdminFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = explode(',', $form->get('roles')->getData());
            $user->setRoles($newRole);
            if ($form->get('remake_pass')->getData() != null) {
                $newPwd = 'changePassword!!!';
                $password = $encoder->hashPassword($user, $newPwd);
                $user->setPassword($password);
            }
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
    public function delete(Request $request, ManagerRegistry $doctrine, String $id)
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([ 'id' => $id ]);

        $entityManager->remove($user);
        $entityManager->flush();

        // Retour à la liste
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
