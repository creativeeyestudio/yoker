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
        $block_manager = $this->BlockManager($block, $request, "header.html.twig", 'add_admin_header');
        return $block_manager;
    }

    #[Route('/admin/footer', name: 'add_admin_footer')]
    public function footer_manage(CommonBlocksService $block, Request $request): Response
    {
        $block_manager = $this->BlockManager($block, $request, "footer.html.twig", 'add_admin_footer');
        return $block_manager;
    }

    private function BlockManager(CommonBlocksService $block, Request $request, String $block_html, String $redirect_route){
        // Fichiers
        $file = '../templates/webpages/blocks/fr/' . $block_html;
        $file_en = '../templates/webpages/blocks/en/' . $block_html;
        // Contenu
        $content = file_get_contents($file);
        $content_en = file_get_contents($file_en);
        $form = $block->BlockManager($request, $file, $file_en);
        // Submit du form
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute($redirect_route);
        }
        // Return
        return $this->render('common_blocks/' . $block_html, [
            'form' => $form->createView(),
            'content' => $content,
            'content_en' => $content_en,
        ]);
    }
}
