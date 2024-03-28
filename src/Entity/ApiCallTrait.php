<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ApiCallTrait
{
    use ProcessTrait;

    /**
     * @ORM\Column(length=20)
     */
    #[ORM\Column(length: 20)]
    private ?string $origin = null;

    /**
     * @ORM\Column
     */
    #[ORM\Column]
    private ?int $statusCode = null;

    /**
     * @ORM\Column(length=10)
     */
    #[ORM\Column(length: 10)]
    private ?string $method = null;

    /**
     * The routeUrl property contains the full url used to call the API
     * On other hand, the route alias will be stored in the type property from the ProcessTrait. Storing the route alias this way can help you build
     * api stats call on top this trait/interface.
     *
     * @ORM\Column(type=Types::TEXT)
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $routeUrl = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    private ?array $inputData = null;

    /**
     * @ORM\Column(type=Types::JSON, nullable=true)
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array|string|null $outputResponse = null;

    public function __toString(): string
    {
        return $this->statusCode . ' - ' . $this->getRouteUrl();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getRouteUrl(): ?string
    {
        return $this->routeUrl;
    }

    public function setRouteUrl(string $routeUrl): static
    {
        $this->routeUrl = $routeUrl;

        return $this;
    }

    public function getInputData(): ?array
    {
        return $this->inputData;
    }

    public function setInputData(?array $inputData): static
    {
        $this->inputData = $inputData;

        return $this;
    }

    public function getOutputResponse(): array|string|null
    {
        return $this->outputResponse;
    }

    public function setOutputResponse(array|string|null $outputResponse): static
    {
        $this->outputResponse = $outputResponse;

        return $this;
    }
}
