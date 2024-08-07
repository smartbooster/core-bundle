<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Enum\ProcessStatusEnum;
use Smart\CoreBundle\Utils\DateUtils;

trait ProcessTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(length=50)
     */
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    /**
     * @ORM\Column(type=Types::DATETIME_MUTABLE)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    /**
     * @ORM\Column(type=Types::DATETIME_MUTABLE, nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endedAt = null;

    /**
     * Duration in milliseconds between the start and end times
     *
     * @ORM\Column(nullable=true, options={"unsigned":true})
     */
    #[ORM\Column(nullable: true, options: ['unsigned' => true])]
    private ?int $duration = null;

    /**
     * @ORM\Column(length=15, enumType=ProcessStatusEnum::class)
     */
    #[ORM\Column(length: 15, enumType: ProcessStatusEnum::class)]
    private ?ProcessStatusEnum $status = null;

    /**
     * @ORM\Column(type=Types::TEXT, nullable=true)
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    private ?array $logs = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    /**
     * Used to know when we wanted to retry this process (creating a new dedicated process based on this one)
     *
     * @ORM\Column(type=Types::DATETIME_MUTABLE, nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $restartedAt = null;

    public function isOngoing(): bool
    {
        return $this->getStatus() === ProcessStatusEnum::ONGOING;
    }

    public function isSuccess(): bool
    {
        return $this->getStatus() === ProcessStatusEnum::SUCCESS;
    }

    public function isError(): bool
    {
        return $this->getStatus() === ProcessStatusEnum::ERROR;
    }

    public function canRestart(): bool
    {
        return $this->isError() and $this->getRestartedAt() === null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getDurationAsString(): ?string
    {
        return DateUtils::millisecondsToString($this->duration);
    }

    public function getDurationSeconds(): ?int
    {
        if ($this->getDuration() === null) {
            return null;
        }

        return $this->getDuration() / 1000;
    }

    /**
     * Variant to use for process where printing milliseconds doesn't matter
     */
    public function getDurationSecondsAsString(): ?string
    {
        return DateUtils::secondsToString($this->getDurationSeconds());
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStatus(): ?ProcessStatusEnum
    {
        return $this->status;
    }

    public function getStatusAsString(): string
    {
        return $this->status->value;
    }

    public function setStatus(ProcessStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getLogs(): ?array
    {
        return $this->logs;
    }

    public function getLogsAsHtml(): ?string
    {
        $logs = $this->getLogs();
        if (empty($logs)) {
            return null;
        }

        return implode('<br>', $logs);
    }

    public function setLogs(?array $logs): static
    {
        $this->logs = $logs;

        return $this;
    }

    public function addLog(mixed $log): self
    {
        if ($this->logs == null) {
            $this->logs = [];
        }
        $this->logs[] = $log;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function addData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Use this if you wish to store the exception trace on the internal data of the process
     */
    public function addExceptionTraceData(\Exception $e): void
    {
        $this->data['exception_trace'] = $e->getTrace();
    }

    public function getRestartedAt(): ?\DateTime
    {
        return $this->restartedAt;
    }

    public function setRestartedAt(?\DateTime $restartedAt): void
    {
        $this->restartedAt = $restartedAt;
    }
}
