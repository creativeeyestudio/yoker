<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebPagesRedirectController extends AbstractController
{
    #[Route('/', name: 'web_redirect')]
    public function redirectBase(): Response{
        return $this->redirectToRoute('web_index');
    }
}
