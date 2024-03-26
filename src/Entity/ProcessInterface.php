<?php

namespace Smart\CoreBundle\Entity;

use Smart\CoreBundle\Enum\ProcessStatusEnum;

interface ProcessInterface
{
    public function getId(): ?int;

    public function getType(): ?string;

    public function setType(string $type): static;

    public function getStartedAt(): ?\DateTimeInterface;

    public function setStartedAt(\DateTimeInterface $startedAt): static;

    public function getEndedAt(): ?\DateTimeInterface;

    public function setEndedAt(?\DateTimeInterface $endedAt): static;

    public function getDuration(): ?int;

    public function getDurationAsString(): ?string;

    public function setDuration(?int $duration): static;

    public function getStatus(): ?ProcessStatusEnum;

    public function getStatusAsString(): string;

    public function setStatus(ProcessStatusEnum $status): static;

    public function getSummary(): ?string;

    public function setSummary(string $summary): static;

    public function getLogs(): ?array;

    public function setLogs(?array $logs): static;

    public function addLog(mixed $log): self;

    public function getData(): ?array;

    public function setData(?array $data): static;

    public function addData(string $key, mixed $value): void;
}
