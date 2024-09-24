<?php

namespace Smart\CoreBundle\Command;

use App\Entity\Monitoring\ApiCall;
use App\Entity\Monitoring\Cron;
use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Config\IniOverrideConfig;
use Smart\CoreBundle\Entity\ProcessInterface;
use Smart\CoreBundle\Monitoring\ProcessMonitor;
use Smart\CoreBundle\Utils\StringUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
#[AsCommand(
    name: 'cron:smart-entity-cleanup',
    description: 'Database entity cleanup command based on the bundle configuration to completely remove entities or partially delete some data.',
)]
class EntityCleanupCommand extends Command
{
    public const BATCH_SIZE = 50;

    /**
     * @var array
     * Example of configuration to put on your smart_core.yaml :
     *  smart_core:
     *      entity_cleanup_command_configs:
     *          smart_entity_cleanup:
     *              older_than: 1 week
     *          count_organization:
     *              older_than: 1 day
     *          api_organization_update:
     *              older_than: 1 week
     *              class: api_call
     *              where: o.status = 'success'
     *              properties_to_clean:
     *                  - logs
     *                  - data
     *                  - inputData
     *                  - headers
     *                  - outputResponse
     *          simulation_3_years:
     *              older_than: 3 years
     *              older_than_property: updatedAt
     *              class: App\Entity\Simulation
     */
    private array $commandConfigs = [];

    public function __construct(
        private readonly ProcessMonitor $processMonitor,
        protected readonly IniOverrideConfig $config,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = Command::SUCCESS;
        $io = new SymfonyStyle($input, $output);
        $this->processMonitor->setConsoleIo($io);
        $this->config->increaseMemoryLimit();
        $process = $this->processMonitor->start(new Cron($this->getName()), true); // @phpstan-ignore-line

        try {
            $nbCleanedEntities = 0;
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($this->commandConfigs as $key => $config) {
                $targetClass = $this->getTargetClass($config);
                $olderThanProperty = $config['older_than_property'];
                $olderThan = $config['older_than'];
                $dateTime = new \DateTime();
                $dateTime->modify('-' . $olderThan);

                $qb = $this->entityManager->getRepository($targetClass)->createQueryBuilder('o')
                    ->andWhere("o.$olderThanProperty <= :started_at")
                    ->setParameter('started_at', $dateTime)
                ;
                // MDT if the entity implements the ProcessInterface, we used the key of the config to filter the type
                $isProcess = in_array(ProcessInterface::class, class_implements($targetClass));
                if ($isProcess) {
                    if ($targetClass === Cron::class) { // @phpstan-ignore-line
                        $key = str_replace('_', '-', $key);
                    }
                    $qb->andWhere('o.type = :type')->setParameter('type', $key);
                }
                $whereCondition = $config['where'];
                if ($whereCondition !== null) {
                    $qb->andWhere($whereCondition);
                }
                $entities = $qb->getQuery()->getResult();
                $nbEntities = count($entities);
                $translatedTargetClass = $this->translator->trans('label.' . StringUtils::getEntityShortName($targetClass) . 's');
                $this->processMonitor->logSection(sprintf(
                    "%d %s%s à nettoyer depuis %s%s",
                    $nbEntities,
                    $translatedTargetClass,
                    $isProcess ? (' ' . $key) : '',
                    $olderThan,
                    $whereCondition ? " (avec la condition $whereCondition)" : ''
                ));

                $i = 1;
                $propertiesToClean = $config['properties_to_clean'];
                $removeEntity = empty($propertiesToClean);
                foreach ($entities as $entity) {
                    $this->processMonitor->log(sprintf("%d) #%d début nettoyage ...", $i, $entity->getId()));
                    if ($removeEntity) {
                        $this->entityManager->remove($entity);
                    } else {
                        foreach ($propertiesToClean as $property) {
                            $propertyAccessor->setValue($entity, $property, null);
                        }
                    }
                    if ($i % self::BATCH_SIZE === 0) {
                        $this->entityManager->flush();
                    }
                    $nbCleanedEntities++;
                    $i++;
                }
                $this->entityManager->flush();
                if ($nbEntities > 0) {
                    $this->processMonitor->log("Fin du nettoyage des $translatedTargetClass.");
                }
            }

            $this->processMonitor->logSuccess("$nbCleanedEntities entités nettoyées.");
        } catch (\Exception $e) {
            $exitCode = Command::FAILURE;
            $this->processMonitor->logException($e);
        } finally {
            $this->processMonitor->end($process, $exitCode === Command::SUCCESS);
        }

        return $exitCode;
    }

    /**
     * @return class-string<object>
     */
    private function getTargetClass(array $config): string
    {
        $class = $config['class'];
        if ($class === 'cron') {
            return Cron::class; // @phpstan-ignore-line
        } elseif ($class === 'api_call') {
            return ApiCall::class; // @phpstan-ignore-line
        } else {
            return $class;
        }
    }

    public function setCommandConfigs(array $commandConfigs): void
    {
        $this->commandConfigs = $commandConfigs;
    }
}
