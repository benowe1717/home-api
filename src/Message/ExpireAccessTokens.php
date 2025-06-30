<?php

/**
 * Symfony Message for Expiring Access Tokens
 *
 * PHP version 8.4
 *
 * @category  Message
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Message;

/**
 * Symfony Message for Expiring Access Tokens
 *
 * PHP version 8.4
 *
 * @category  Message
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
final class ExpireAccessTokens
{
    private int $now;

    /**
     * ExpireAccessTokens constructor
     **/
    public function __construct()
    {
        $this->now = time();
    }

    /**
     * Getter for $now property
     *
     * @return int
     **/
    public function getNow(): int
    {
        return $this->now;
    }
}
