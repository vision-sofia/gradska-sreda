<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Logout
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function logout(Request $request): void
    {
        $this->tokenStorage->setToken(null);

        if ($request->getSession()) {
            $request->getSession()->invalidate();
        }
    }
}
