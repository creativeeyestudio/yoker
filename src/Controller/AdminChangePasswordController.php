<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminChangePasswordController extends AbstractController
{
    #[Route('/admin/manage-infos', name: 'app_admin_change_password')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $oldPassword)) {
                $newPassword = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $newPassword);

                $user->setPassword($password);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->render('admin_change_password/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
