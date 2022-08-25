<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebPagesIndexController extends AbstractController
{
    #[Route('/web/pages/index', name: 'app_web_pages_index')]
    public function index(): Response
    {
        return $this->render('web_pages_index/index.html.twig', [
            'controller_name' => 'WebPagesIndexController',
        ]);
    }
}
