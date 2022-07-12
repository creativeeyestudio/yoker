<?php

namespace App\Controller;

use App\Form\FileUploadFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminFilesController extends AbstractController
{
    #[Route('/admin/medias', name: 'app_admin_files')]
    public function index(): Response
    {

        $form = $this->createForm(FileUploadFormType::class);
        return $this->render('admin_files/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdminFilesController',
        ]);
    }
}
