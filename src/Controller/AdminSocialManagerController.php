<?php

namespace App\Controller;

use App\Entity\SocialManager;
use App\Form\SocialManagerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSocialManagerController extends AbstractController
{
    #[Route('/admin/social-manager', name: 'app_admin_social_manager')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $social = $em->getRepository(SocialManager::class)->find(1);
        
        if (!$social) {
            $social = new SocialManager();
        }

        $form = $this->createForm(SocialManagerType::class, $social);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($social);
            $em->flush();

            return $this->redirectToRoute('app_admin_social_manager');
        }

        return $this->render('admin_social_manager/index.html.twig', [
            'form' => $form
        ]);
    }
}
