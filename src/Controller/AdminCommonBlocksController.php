<?php

namespace App\Controller;

use App\Form\CommonBlockFormType;
use App\Services\CommonBlocksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommonBlocksController extends AbstractController
{
    #[Route('/admin/header', name: 'add_admin_header')]
    public function header_manage(CommonBlocksService $block, Request $request): Response
    {
        $file = '../templates/webpages/blocks/header.html.twig';
        $content = file_get_contents($file);
        $form = $block->BlockManager($request, $file);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('add_admin_header');
        }

        return $this->render('common_blocks/header.html.twig', [
            'form' => $form->createView(),
            'content' => $content,
            'controller_name' => 'AdminCommonBlocksController',
        ]);
    }

    #[Route('/admin/footer', name: 'add_admin_footer')]
    public function footer_manage(CommonBlocksService $block, Request $request): Response
    {
        $file = '../templates/webpages/blocks/footer.html.twig';
        $content = file_get_contents($file);
        $form = $block->BlockManager($request, $file);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('add_admin_footer');
        }
        
        return $this->render('common_blocks/footer.html.twig', [
            'form' => $form->createView(),
            'content' => $content,
            'controller_name' => 'AdminCommonBlocksController',
        ]);
    }
}
