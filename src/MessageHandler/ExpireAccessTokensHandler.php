<?php

/**
 * Symfony Message Handler for ExpireAccessTokens Message
 *
 * PHP version 8.5
 *
 * @category  MessageHandler
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 */

namespace App\MessageHandler;

use App\Entity\AccessToken;
use App\Message\ExpireAccessTokens;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Symfony Message Handler for ExpireAccessTokens Message
 *
 * PHP version 8.5
 *
 * @category  MessageHandler
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 */
#[AsMessageHandler]
final class ExpireAccessTokensHandler
{
    private EntityManagerInterface $entityManager;

    /**
     * ExpireAccessTokensHandler constructor
     *
     * @param EntityManagerInterface $entityManager The Entity Manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return all Access Token objects found in the database
     *
     * @return array
     */
    private function getAccessTokens(): array
    {
        $repo = $this->entityManager->getRepository(AccessToken::class);
        return $repo->findAll();
    }

    /**
     * Take action on all emitted ExpireAccessTokens messages
     *
     * @param ExpireAccessTokens $message The ExpireAccessTokens message
     *
     * @return void
     */
    public function __invoke(ExpireAccessTokens $message): void
    {
        $now = $message->getNow();
        $accessTokens = $this->getAccessTokens();

        /**
         * The Access Token Entity to operate on
         *
         * @var AccessToken $accessToken
         */
        foreach ($accessTokens as $accessToken) {
            $expiresAt = $accessToken->getExpires();
            if ($now >= $expiresAt) {
                $this->entityManager->remove($accessToken);
                $this->entityManager->flush();
            }
        }
    }
}
