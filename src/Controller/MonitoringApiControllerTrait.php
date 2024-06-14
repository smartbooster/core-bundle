<?php

namespace Smart\CoreBundle\Controller;

use App\Entity\Monitoring\ApiCall;
use Smart\CoreBundle\Entity\ApiCallInterface;
use Smart\CoreBundle\Monitoring\ApiCallMonitor;
use Smart\CoreBundle\Monitoring\ProcessMonitor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait MonitoringApiControllerTrait
{
    protected ApiCallMonitor $apiCallMonitor;
    protected ?ApiCallInterface $apiCall = null;

    protected function startApiCall(Request $request, string $origin): void
    {
        $this->apiCall = $this->apiCallMonitor->start(new ApiCall(), $request);
        $this->apiCall->setOrigin($origin);
    }

    protected function endApiCall(mixed $outputResponse = null, int $statusCode = Response::HTTP_OK, ?\Exception $e = null): void
    {
        if ($this->apiCall instanceof ApiCallInterface) {
            $this->apiCall->setOutputResponse($outputResponse);
            if ($e !== null) {
                $this->apiCallMonitor->logException($e);
            }
            $this->apiCallMonitor->end($this->apiCall, $statusCode);
        }
    }

    protected function getProcessMonitor(): ProcessMonitor
    {
        return $this->apiCallMonitor->getProcessMonitor();
    }

    #[Required]
    public function setApiCallMonitor(ApiCallMonitor $apiCallMonitor): void
    {
        $this->apiCallMonitor = $apiCallMonitor;
    }
}
