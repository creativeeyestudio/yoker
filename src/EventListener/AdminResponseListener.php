<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AdminResponseListener
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Vérifiez si la requête concerne une URL protégée par access_control
        if ($this->isProtectedUrl($request)) {

            // Vérifiez si l'utilisateur est authentifié et s'il n'est pas validé
            $user = $this->security->getUser();
            if ($user !== null && !$user->isVerified()) {
                $event->setResponse(new RedirectResponse('/login'));
            }
        }
    }


    private function isProtectedUrl($request): bool
    {
        $access_control = $request->attributes->get('token');

        if ($access_control) {
            return true;
        }

        return false;
    }
}
