<?php

namespace App\Security;

use App\AppMain\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private EntityManagerInterface $em;
    private RouterInterface $router;
    private EncoderFactoryInterface $encoder;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        EncoderFactoryInterface $encoder
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->encoder = $encoder;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): bool
    {
        return 'app.login' === $request->attributes->get('_route')
                && $request->isMethod('POST');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $username = $request->get('_username');
        $password = $request->get('_password');

        return ['_username' => $username, '_password' => $password];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = (string) $credentials['_username'];

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $password = isset($credentials['_password']) ? $credentials['_password'] : null;

        $encoder = $this->encoder->getEncoder($user);

        $validPassword = $encoder->isPasswordValid(
            $user->getPassword(),
            $password,
            $user->getSalt()
        );

        if ($validPassword) {
            return true;
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('app.map'));
    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('app.login');
    }

    protected function getDefaultSuccessRedirectUrl(): string
    {
        return $this->router->generate('app.map');
    }
}
