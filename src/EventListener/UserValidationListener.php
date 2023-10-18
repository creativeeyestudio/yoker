<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class UserValidationListener
{
    private $security;
    private $requestStack;
    
    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();
        $currentUrl = $this->requestStack->getCurrentRequest()->getPathInfo();
        
        if ($user && $user->is_verified == false && strpos($currentUrl, '/admin') === 0) {
            $response = new Response('Votre compte n\'est pas encore validé.', Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
