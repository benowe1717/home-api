<?php

/**
 * Symfony Controller for /health Route
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

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Symfony Controller for /health Route
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
final class HealthCheckController extends AbstractController
{
    /**
     * Read the version number from a local file
     *
     * @return string
     **/
    private function readVersionFile(): string
    {
        try {
            $version = file_get_contents('../version');
            return trim($version);
        } catch (Exception) {
            return 'unknown';
        }
    }

    /**
     * /health app_health_check Route
     *
     * @return JsonResponse
     **/
    #[Route('/health', name: 'app_health_check')]
    public function index(): JsonResponse
    {
        return $this->json(
            [
                'status' => 'success',
                'version' => $this->readVersionFile()
            ]
        );
    }
}
