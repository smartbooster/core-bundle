<?php

namespace Smart\CoreBundle\Sentry;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 *
 * Disable send event to Sentry if requested by CleverCloud healthcheck
 * https://docs.sentry.io/product/accounts/quotas/manage-event-stream-guide/#1-sdk-filtering-beforesend
 * https://docs.sentry.io/platforms/php/guides/symfony/configuration/symfony-options/#callables
 */
class SentryCallback
{
    protected const REQUEST_WITH_HEADERS_TO_IGNORE = [
        'x-clevercloud-monitoring'
    ];

    public function getBeforeSend(): callable
    {
        return function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
            if (
                isset($event->getRequest()['headers']) && !empty(array_intersect(
                    array_keys($event->getRequest()['headers']),
                    self::REQUEST_WITH_HEADERS_TO_IGNORE
                ))
            ) {
                return null;
            }

            return $event;
        };
    }
}
