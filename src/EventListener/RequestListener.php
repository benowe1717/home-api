<?php

/**
 * Symfony Event Listener for kernel.request Events
 *
 * PHP version 8.4
 *
 * @category  EventListener
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * Symfony Event Listener for kernel.request Events
 *
 * PHP version 8.4
 *
 * @category  EventListener
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
final class RequestListener
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
    #[AsEventListener]
    public function onRequestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $limiter = $this->apiV1Limiter->create($request->getUser());
        $limit = $limiter->consume(1);

        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-RetryAfter' => $limit->getRetryAfter()->format(
                'Y-m-d H:i:s'
            ),
            'X-RateLimit-Limit' => $limit->getLimit()
        ];

        if (false === $limit->isAccepted()) {
            $result = array(
                'result' => 'failed',
                'reason' => 'Rate Limit exceeded!'
            );
            $response = new JsonResponse(
                $result,
                JsonResponse::HTTP_TOO_MANY_REQUESTS,
                $headers
            );
            $event->setResponse($response);
        }
    }
}
