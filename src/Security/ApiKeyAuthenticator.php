<?php

/**
 * Symfony CustomAuthenticator for API Keys
 *
 * PHP version 8.5
 *
 * @category  CustomAuthenticator
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

/**
 * Symfony CustomAuthenticator for API Keys
 *
 * PHP version 8.5
 *
 * @category  CustomAuthenticator
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.2
 * @link      https://github.com/benowe1717/home-api
 */
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManagerInterface;

    /**
     * ApiKeyAuthenticator constructor
     *
     * @param EntityManagerInterface $entityManagerInterface The Entity Manager
     */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Find the User for the given Access Token
     *
     * @param string $accessToken The Access Token
     *
     * @return ?User
     */
    private function getUserByAccessToken(string $accessToken): ?User
    {
        $repo = $this->entityManagerInterface->getRepository(User::class);
        return $repo->findUserByAccessToken($accessToken);
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     *
     * @param Request $request The HTTP Request
     *
     * @return ?bool
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    /**
     * Authenticate a User based on the API Key
     *
     * @param Request $request The HTTP Request
     *
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $authorization = $request->headers->get('Authorization');

        if (null === $authorization) {
            throw new CustomUserMessageAuthenticationException(
                'No Authentication provided!'
            );
        }

        $pattern = '^(?P<scope>\w+)\s+(?P<key>[A-z0-9\=\+\/]+)$';
        if (!preg_match("/{$pattern}/", $authorization, $matches)) {
            throw new CustomUserMessageAuthenticationException(
                'Invalid Authorization provided!'
            );
        }

        if ('basic' === strtolower($matches['scope'])) {
            $email = $request->getUser();
            $password = $request->getPassword();

            $passport = new Passport(
                new UserBadge($email),
                new PasswordCredentials($password)
            );
            if (null === $passport) {
                throw new CustomUserMessageAuthenticationException(
                    'Invalid Username or Password!'
                );
            }

            $userIdentifier = $email;
        } elseif ('bearer' === strtolower($matches['scope'])) {
            $user = $this->getUserByAccessToken($matches['key']);
            if (null === $user) {
                throw new CustomUserMessageAuthenticationException(
                    'Invalid Access Token!'
                );
            }

            $userIdentifier = $user->getUserIdentifier();
        } else {
            throw new CustomUserMessageAuthenticationException(
                'Invalid Authorization scope provided!'
            );
        }

        $request->headers->set('PHP_AUTH_USER', $userIdentifier);

        return new SelfValidatingPassport(new UserBadge($userIdentifier));
    }

    /**
     * Take specific actions when authentication succeeds
     *
     * @param Request        $request      The HTTP Request
     * @param TokenInterface $token        The Token Interface
     * @param string         $firewallName The Firewall to authenticate to
     *
     * @return ?Response
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    // public function start(Request $request, ?AuthenticationException $authException = null): Response
    // {
    //     /*
    //      * If you would like this class to control what happens when an anonymous user accesses a
    //      * protected page (e.g. redirect to /login), uncomment this method and make this class
    //      * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //      *
    //      * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //      */
    // }
}
