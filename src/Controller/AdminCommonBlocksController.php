<?php

namespace App\Controller;

use App\Form\CommonBlockFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommonBlocksController extends AbstractController
{
    #[Route('/admin/header', name: 'add_admin_header')]
    public function header_manage(): Response
    {
        $form = $this->createForm(CommonBlockFormType::class);
        return $this->render('common_blocks/header.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdminCommonBlocksController',
        ]);
    }

    #[Route('/admin/footer', name: 'add_admin_footer')]
    public function footer_manage(): Response
    {
        $form = $this->createForm(CommonBlockFormType::class);
        return $this->render('common_blocks/footer.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdminCommonBlocksController',
        ]);
    }
}
