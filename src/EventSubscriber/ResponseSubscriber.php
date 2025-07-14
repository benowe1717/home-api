<?php

/**
 * Symfony Event Subscriber for kernel.response Events
 *
 * PHP version 8.4
 *
 * @category  EventSubscriber
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * Symfony Event Subscriber for kernel.response Events
 *
 * PHP version 8.4
 *
 * @category  EventSubscriber
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
class ResponseSubscriber implements EventSubscriberInterface
{
    private RateLimiterFactoryInterface $apiV1Limiter;

    /**
     * RequestListener constructor
     *
     * @param RateLimiterFactoryInterface $apiV1Limiter The api_v1.limiter
     **/
    public function __construct(RateLimiterFactoryInterface $apiV1Limiter)
    {
        $this->apiV1Limiter = $apiV1Limiter;
    }

    /**
     * Event Listener for kernel.request used to check rate limiting across the
     * entire application.
     *
     * @param RequestEvent $event The HTTP Request Event
     *
     * @return void
     **/
    public function onResponseEvent(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $limiter = $this->apiV1Limiter->create($request->getUser());
        $limit = $limiter->consume(0);

        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-RetryAfter' => $limit->getRetryAfter()->format(
                'Y-m-d H:i:s'
            ),
            'X-RateLimit-Limit' => $limit->getLimit()
        ];

        $response = $event->getResponse();
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
    }

    /**
     * Return all subscribed events
     *
     * @return array
     **/
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
