<?php

namespace Smart\CoreBundle\Monitoring;

use Smart\CoreBundle\Entity\ApiCallInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class ApiCallMonitor
{
    private array $restartAllowedRoutes = [];

    public function __construct(private readonly ProcessMonitor $processMonitor, private readonly HttpClientInterface $httpClient)
    {
    }

    public function start(ApiCallInterface $apiCall, Request $request, bool $flush = false): ApiCallInterface
    {
        $this->processMonitor->start($apiCall, $flush);
        $apiCall->setMethod($request->getMethod());
        $apiCall->setRouteUrl($request->getUri());
        $apiCall->setType($request->attributes->get('_route'));

        $contentTypeFormat = $request->getContentTypeFormat();
        $apiCall->setContentTypeFormat($contentTypeFormat);
        if ($contentTypeFormat === 'json') {
            $requestContent = $request->getContent();
            $decodedContent = json_decode($requestContent, true);
            $apiCall->setInputData($decodedContent);
            if ($decodedContent === null) { // MDT in case the JSON failed json_decode we save the raw request content
                $apiCall->setRawContent($requestContent);
            }
        } else {
            $apiCall->setInputData($request->request->all());
        }

        $apiCall->setHeaders($request->headers->all());

        return $apiCall;
    }

    public function end(ApiCallInterface $apiCall, int $statusCode, bool $flush = true): void
    {
        $apiCall->setStatusCode($statusCode);
        $this->processMonitor->end($apiCall, $statusCode >= Response::HTTP_CONTINUE && $statusCode < Response::HTTP_BAD_REQUEST, $flush);
    }

    public function restart(ApiCallInterface $apiCall): ResponseInterface
    {
        $route = $apiCall->getType();
        if (!in_array($route, $this->restartAllowedRoutes)) {
            throw new AccessDeniedHttpException("The API route '$route' is not allowed to be restarted. " .
                "Add it to the `smart_core.monitoring_api_restart_allowed_routes` config if you want to restart it.");
        }

        $options = ['headers' => $apiCall->getHeaders()];
        $inputData = $apiCall->getInputData();
        if ($apiCall->hasInputData() && $apiCall->isJson()) {
            $options['json'] = $inputData;
        } elseif ($apiCall->hasInputData()) { // form content-type
            $options['body'] = $inputData;
        }

        $response = $this->httpClient->request($apiCall->getMethod(), $apiCall->getRouteUrl(), $options);
        // We call getStatusCode to initialize the response and ensure it does not throw an Exception from CommonResponseTrait::checkStatusCode
        $responseCode = $response->getStatusCode();

        // Now that we requested the API, we can flag the process as restarted
        $this->processMonitor->restart($apiCall);

        return $response;
    }

    public function logException(\Exception $e): void
    {
        $this->getProcessMonitor()->logException($e);
    }

    public function getProcessMonitor(): ProcessMonitor
    {
        return $this->processMonitor;
    }

    public function getRestartAllowedRoutes(): array
    {
        return $this->restartAllowedRoutes;
    }

    public function setRestartAllowedRoutes(array $restartAllowedRoutes): void
    {
        $this->restartAllowedRoutes = $restartAllowedRoutes;
    }
}
