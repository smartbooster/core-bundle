<?php

namespace Smart\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Config\IniOverrideConfig;
use Smart\CoreBundle\Formatter\PhpFormatter;
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

    /**
     * This route help us to detect that the date.timezone ini options is well set on CleverCloud
     */
    public function dateFormatting(): Response
    {
        $format = PhpFormatter::DATETIME_WITH_SECONDS_FR;
        $datetime = new \DateTime();
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::SHORT, \IntlDateFormatter::LONG);

        $content = "CURRENT TIME\n\n";
        $content .= "default date function : \n" . date($format) . "\n";
        $content .= "Datetime format : \n" . $datetime->format($format) . "\n";
        $content .= "date_format function : \n" . date_format($datetime, ($format)) . "\n";
        $content .= "IntlDateFormatter format : \n" . $formatter->format($datetime) . "\n";
        $content .= "Timezone name : \n" . $datetime->getTimezone()->getName() . "\n";

        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
