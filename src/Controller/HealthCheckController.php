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
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
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
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.2
 * @link      https://github.com/benowe1717/home-api
 **/
final class HealthCheckController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * HealthCheckController constructor
     *
     * @param EntityManagerInterface $entityManager The Entity Manager
     **/
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Validate that a connection to the database can be established
     *
     * @return bool
     **/
    private function testDatabaseConnection(): bool
    {
        try {
            $this->entityManager->getConnection()->connect();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

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
        $result = $this->testDatabaseConnection();
        if (false === $result) {
            $result = array(
                'status' => 'failed',
                'reason' => 'Database connection failed!'
            );
            return $this->json($result, JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->json(
            [
                'status' => 'success',
                'version' => $this->readVersionFile()
            ]
        );
    }
}
