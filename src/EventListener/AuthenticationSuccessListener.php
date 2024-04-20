<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationSuccessListener
{
    private $tokenStorage;
    private $requestStack;

    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    #[AsEventListener(event: 'security.interactive_login')]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        // Vérifiez si l'utilisateur est vérifié
        if (!$token->getUser()->isVerified()) {
            $this->tokenStorage->setToken(null);
            $request->getSession()->invalidate();
            throw new CustomUserMessageAuthenticationException(
                'Votre compte n\'a pas encore été vérifié. Veuillez vérifier votre adresse e-mail.'
            );
        }
    }
}
