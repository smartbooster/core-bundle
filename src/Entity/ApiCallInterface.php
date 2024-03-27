<?php

namespace Smart\CoreBundle\Entity;

interface ApiCallInterface extends ProcessInterface
{
    public function getOrigin(): ?string;

    public function setOrigin(string $origin): static;

    public function getStatusCode(): ?int;

    public function setStatusCode(int $statusCode): static;

    public function getMethod(): ?string;

    public function setMethod(string $method): static;

    public function getRouteUrl(): ?string;

    public function setRouteUrl(string $routeUrl): static;

    public function getInputData(): ?array;

    public function setInputData(?array $inputData): static;

    public function getOutputResponse(): array|string|null;

    public function setOutputResponse(array|string|null $outputResponse): static;
}
