<?php

namespace App\Controller;

use App\Entity\GlobalSettings;
use App\Form\GlobalSettingsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSettingsController extends AbstractController
{
    #[Route('/admin/settings', name: 'admin_settings')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $settings = $em->getRepository(GlobalSettings::class)->findOneBy(['id' => 0]);

        if (!$settings) {
            throw $this->createNotFoundException("Paramètres globaux non trouvés");
        }

        $form = $this->createForm(GlobalSettingsFormType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoInput = $form->get('logo')->getData();
            if ($logoInput) {
                $ext = $logoInput->guessExtension();
                $new_name = 'logotype.' . $ext;
                try {
                    $logoInput->move(
                        'uploads/images/logo',
                        $new_name
                    );
                    $settings->setLogo($new_name);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            $em->flush();
        }

        return $this->render('admin_settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
