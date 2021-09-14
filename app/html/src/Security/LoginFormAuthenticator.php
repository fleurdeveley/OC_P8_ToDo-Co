<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login';

    private $urlGenerator;
    private $hasher;
    private $entityManager;
    private $csrfTokenManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordHasherInterface $hasher,
        CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->hasher = $hasher;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    // If the login form is submitted
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    // Retrieve connection information from the request
    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    // Thanks to the credentials information, do I have the username in my database
    public function getUser($credentials): ?User
    {
        // Access identifier, specific to each user, impossible to hack it
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'username' => $credentials['username']
        ]);
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException("Le nom de l'utilisateur n'existe pas.");
        }

        return $user;
    }

    // Check that the password provided matches the database password
    public function checkCredentials($credentials, PasswordAuthenticatedUserInterface $user): bool
    {
        return $this->hasher->isPasswordValid($user, $credentials['password']);
    }

    // Recover the password in the credentials table
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    // Creation of authentication
    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);

        $user = $this->getUser($credentials);

        $username = $user->getUsername();

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        // Class containing information to validate during authentication

        $passport = new Passport(
        // Check username
            new UserBadge($username),

            // Check password
            new PasswordCredentials($request->request->get('_password', '')),
            [
                // Check the validity of the csrfToken
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );

        return $passport;
    }

    // If the username and password are correct, then the user is logged in and then redirected to the home page
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    // Return the url of the login route
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
