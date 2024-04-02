<?php

namespace Smart\CoreBundle\Monitoring;

use Smart\CoreBundle\Entity\ApiCallInterface;
use Smart\CoreBundle\Entity\ProcessInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class ApiCallMonitor
{
    public function __construct(private readonly ProcessMonitor $processMonitor)
    {
    }

    public function start(ApiCallInterface $apiCall, Request $request): ApiCallInterface
    {
        $this->processMonitor->start($apiCall);
        $apiCall->setMethod($request->getMethod());
        $apiCall->setRouteUrl($request->getUri());
        $apiCall->setType($request->attributes->get('_route'));
        $apiCall->setInputData($request->request->all());

        return $apiCall;
    }

    public function end(ApiCallInterface $apiCall, int $statusCode, bool $flush = true): void
    {
        $apiCall->setStatusCode($statusCode);
        $this->processMonitor->end($apiCall, $statusCode >= 200 && $statusCode < 300, $flush);
    }
}
