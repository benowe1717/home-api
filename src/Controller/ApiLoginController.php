<?php

/**
 * Symfony Controller for /api/login Route
 *
 * PHP version 8.4
 *
 * @category  Controller
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Symfony Controller for /api/login Route
 *
 * PHP version 8.4
 *
 * @category  Controller
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
final class ApiLoginController extends AbstractController
{
    private EntityManagerInterface $entityManagerInterface;

    /**
     * ApiLoginController constructor
     *
     * @param EntityManagerInterface $entityManagerInterface The Entity Manager
     **/
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Generate a new AccessToken Entity and tie it to the logged in User
     *
     * @param User $user The logged in User
     *
     * @return AccessToken
     **/
    private function generateAccessToken(User $user): AccessToken
    {
        $accessToken = $user->getAccessToken();

        // If no access token exists, then either they have never requested one
        // or the previous token expired and has been removed
        if (null === $accessToken) {
            $accessToken = new AccessToken();
            $accessToken->setUser($user);
        }

        // if now is before the time of expiration, return the current token as it
        // is still valid
        if (time() < $accessToken->getExpires()) {
            return $accessToken;
        }

        // At this point we have determined that the previous token is either no
        // longer valid or has never existed. It is now time to generate a fresh
        // token and update the database accordingly
        $bytes = random_bytes(128);
        $token = base64_encode($bytes);
        $accessToken->setToken($token);

        $expires = time() + 7200;
        $accessToken->setExpires($expires);

        $this->entityManagerInterface->persist($accessToken);
        $this->entityManagerInterface->flush();

        return $accessToken;
    }

    /**
     * /api/login Route to authenticate a user using their username/password
     *
     * @param CurrentUser $currentUser The User Entityt
     *
     * @return JsonResponse
     **/
    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $currentUser): JsonResponse
    {
        $result = array('result' => 'success');

        if (null === $currentUser) {
            $result['result'] = 'failed';
            $result['reason'] = 'Invalid username or password!';
            return $this->json($result, JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        $accessToken = $this->generateAccessToken($user);

        $result['data'] = array('access_token' => $accessToken->getToken());

        return $this->json($result);
    }
}
