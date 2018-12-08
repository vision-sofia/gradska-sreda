<?php


namespace App\Services\Form;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfTokenValidator
{
    protected $requestStack;
    protected $csrfTokenManager;

    public function __construct(RequestStack $requestStack, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->requestStack = $requestStack;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function isCsrfTokenValid(string $id):bool
    {
        $token = $this->requestStack->getCurrentRequest()->get('_token');

        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }
}