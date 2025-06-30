<?php

/**
 * Symfony Scheduler for Expiring Access Tokens
 *
 * PHP version 8.4
 *
 * @category  Scheduler
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Scheduler;

use App\Message\ExpireAccessTokens;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Symfony Scheduler for Expiring Access Tokens
 *
 * PHP version 8.4
 *
 * @category  Scheduler
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
#[AsSchedule('expire-accesstokens')]
final class ExpireAccessTokensProvider implements ScheduleProviderInterface
{
    /**
     * Get the recurring schedule to emit the desired Message to be handled by
     * the configured Message Handler
     *
     * @return Schedule
     **/
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('4 hours', new ExpireAccessTokens())
        );
    }
}
