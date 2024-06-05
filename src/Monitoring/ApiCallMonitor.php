<?php

namespace Smart\CoreBundle\Monitoring;

use Smart\CoreBundle\Entity\ApiCallInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class ApiCallMonitor
{
    public function __construct(private readonly ProcessMonitor $processMonitor)
    {
    }

    public function start(ApiCallInterface $apiCall, Request $request, bool $flush = false): ApiCallInterface
    {
        $this->processMonitor->start($apiCall, $flush);
        $apiCall->setMethod($request->getMethod());
        $apiCall->setRouteUrl($request->getUri());
        $apiCall->setType($request->attributes->get('_route'));
        $apiCall->setInputData($request->request->all());

        return $apiCall;
    }

    public function end(ApiCallInterface $apiCall, int $statusCode, bool $flush = true): void
    {
        $apiCall->setStatusCode($statusCode);
        $this->processMonitor->end($apiCall, $statusCode >= Response::HTTP_CONTINUE && $statusCode < Response::HTTP_BAD_REQUEST, $flush);
    }

    public function getProcessMonitor(): ProcessMonitor
    {
        return $this->processMonitor;
    }
}
