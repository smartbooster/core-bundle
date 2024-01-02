<?php

namespace Smart\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Config\IniOverrideConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MonitoringController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    /**
     * Test route to monitor project uptime with database connection.
     */
    public function uptime(): Response
    {
        return new JsonResponse([
            'mysql_now' => $this->entityManager->getConnection()->prepare("SELECT NOW()")->executeQuery()->fetchOne()
        ]);
    }

    public function phpinfo(): void
    {
        phpinfo();
        die;
    }

    public function simulateIniOverride(IniOverrideConfig $config): Response
    {
        $data = ['memory_limit_default' => $config->getCurrentMemoryLimit()];

        $config->increaseMemoryLimit();
        $data['memory_limit_increased'] = $config->getCurrentMemoryLimit();
        sleep(5); // Fake batch processing
        $data['memory_limit_value_after_fake_batch'] = $config->getCurrentMemoryLimit();

        return new JsonResponse($data);
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
