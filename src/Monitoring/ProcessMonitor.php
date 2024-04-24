<?php

namespace Smart\CoreBundle\Monitoring;

use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Entity\ProcessInterface;
use Smart\CoreBundle\Enum\ProcessStatusEnum;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class ProcessMonitor
{
    protected ?ProcessInterface $process = null;
    protected ?SymfonyStyle $consoleIo = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * If your process is long or asynchrone you might want to flush it on start, if so pass true to the $flush param
     */
    public function start(ProcessInterface $process, bool $flush = false): ProcessInterface
    {
        $process->setStartedAt(new \DateTime());
        $process->setStatus(ProcessStatusEnum::ONGOING);
        if ($flush) {
            $this->entityManager->persist($process);
            $this->entityManager->flush();
        }
        $this->process = $process;

        return $process;
    }

    public function end(?ProcessInterface $process, bool $isSuccess = true, bool $flush = true): void
    {
        if ($process == null) {
            return;
        }

        $endedAt = new \DateTime();
        $process->setEndedAt($endedAt);
        $process->setDuration((int) $endedAt->format('Uv') - (int) $process->getStartedAt()->format('Uv'));
        if ($isSuccess) {
            $process->setStatus(ProcessStatusEnum::SUCCESS);
        } else {
            $process->setStatus(ProcessStatusEnum::ERROR);
        }

        $this->entityManager->persist($process);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function log(string $message): void
    {
        $this->process?->addLog($message);
        $this->consoleIo?->writeln($message);
    }

    public function logSection(string $message): void
    {
        $this->process?->addLog('--- ' . $message);
        $this->consoleIo?->section($message);
    }

    public function logWarning(string $message): void
    {
        $this->process?->addLog('/!\\ ' . $message);
        $this->consoleIo?->warning($message);
    }

    public function logSuccess(string $message): void
    {
        $this->process?->setSummary($message);
        $this->consoleIo?->success($message);
    }

    public function logException(\Exception $e): void
    {
        $message = $e->getMessage();
        $this->process?->setSummary($message);
        $this->process?->addExceptionTraceData($e);
        $this->consoleIo?->error($message);
    }

    public function processAddData(string $key, mixed $value): void
    {
        $this->process?->addData($key, $value);
    }

    public function getProcess(): ?ProcessInterface
    {
        return $this->process;
    }

    public function setConsoleIo(?SymfonyStyle $consoleIo): void
    {
        $this->consoleIo = $consoleIo;
    }
}
