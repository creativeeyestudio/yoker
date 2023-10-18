<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UserValidationListener
{
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();
        
        if ($user && $user->is_verified == false) {
            $response = new Response('Votre compte n\'est pas encore validé.', Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
