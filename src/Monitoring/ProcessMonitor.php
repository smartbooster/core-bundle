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

    public function start(ProcessInterface $process): ProcessInterface
    {
        $process->setStartedAt(new \DateTime());
        $process->setStatus(ProcessStatusEnum::ONGOING);

        return $process;
    }

    public function end(ProcessInterface $process, bool $isSuccess = true, bool $flush = true): void
    {
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
