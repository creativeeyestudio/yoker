<?php

namespace App\Controller;

use App\Entity\GlobalSettings;
use App\Form\GlobalSettingsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminSettingsController extends AbstractController
{
    #[Route('/admin/settings', name: 'admin_settings')]
    public function index(Request $request, EntityManagerInterface $em, KernelInterface $kernel): Response
    {
        $settings = $em->getRepository(GlobalSettings::class)->find(1);

        if (!$settings) {
            $settings = new GlobalSettings();
        }

        $form = $this->createForm(GlobalSettingsFormType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $logoInput = $form->get('logo')->getData();

            if ($logoInput) {
                $ext = $logoInput->guessExtension();
                $newName = 'logotype.' . $ext;

                $uploadsDirectory = $kernel->getProjectDir() . '/public/uploads/images/logo';
                
                try {
                    $logoInput->move($uploadsDirectory, $newName);
                    $settings->setLogo($newName);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            $em->persist($settings);
            $em->flush();
        }

        return $this->render('admin_settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
