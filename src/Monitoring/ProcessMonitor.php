<?php

namespace Smart\CoreBundle\Monitoring;

use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Entity\ProcessInterface;
use Smart\CoreBundle\Enum\ProcessStatusEnum;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class ProcessMonitor
{
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
}
