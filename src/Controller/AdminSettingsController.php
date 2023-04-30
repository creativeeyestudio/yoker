<?php

namespace App\Controller;

use App\Entity\GlobalSettings;
use App\Form\GlobalSettingsFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSettingsController extends AbstractController
{
    #[Route('/admin/settings', name: 'admin_settings')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $settings = $em->getRepository(GlobalSettings::class)->findOneBy(['id' => 0]);
        $settingForm = $this->createForm(GlobalSettingsFormType::class, $settings);
        $settingForm->handleRequest($request);

        $em = $doctrine->getManager();
        $em->persist($settings);
        $em->flush();

        return $this->render('admin_settings/index.html.twig', [
            'form' => $settingForm
        ]);
    }
}
