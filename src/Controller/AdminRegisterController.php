<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminRegisterController extends AbstractController
{
    #[Route('/admin/register', name: 'app_admin_register')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        $notif = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $doctrine = $doctrine->getManager();
            $doctrine->persist($user);
            $doctrine->flush();

            $notif = "Le compte a bien été crée";
        }

        return $this->render('admin_register/index.html.twig', [
            'form' => $form->createView(),
            'notif' => $notif
        ]);
    }
}
