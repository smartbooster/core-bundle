<?php

namespace Smart\CoreBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Smart\CoreBundle\Entity\Log\HistorizableInterface;
use Smart\CoreBundle\Entity\Log\HistorizableStatusInterface;
use Smart\CoreBundle\Formatter\PhpFormatter;
use Smart\CoreBundle\Logger\HistoryLogger;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class HistoryDoctrineListener
{
    private bool $enabled = true;
    private string $prePersistCode = HistoryLogger::CREATED_CODE;
    private ?array $historyExtraData = null;

    public function __construct(private HistoryLogger $historyLogger)
    {
    }

    // @param typed with LifecycleEventArgs or else the fixture loading won't work
    // Should work with Doctrine\ORM\Event\PrePersistEventArgs after doctrine/orm 2.14 update
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->handleHistory($args, $this->prePersistCode);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->handleHistory($args, HistoryLogger::UPDATED_CODE);
    }

    private function handleHistory(LifecycleEventArgs $args, string $code): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof HistorizableInterface || !$this->enabled || !$entity->isDoctrineListenerEnable()) {
            return;
        }

        $historyData = [];
        if ($code === HistoryLogger::UPDATED_CODE) {
            /** @var PreUpdateEventArgs $args */
            $entityData = $args->getEntityChangeSet();

            // If update with only the history field, we skip the field to avoid duplicate history (email case)
            if (count($entityData) === 1 && isset($entityData['history'])) {
                return;
            }

            $statusDiff = null;
            $isHistorizableStatus = $entity instanceof HistorizableStatusInterface;
            $diffFieldsToSkip = $entity->getHistoryDiffFieldsToSkip();
            foreach ($entityData as $field => $change) {
                if (
                    $field === 'history'
                    || $field === 'updatedAt'
                    || $field === 'updatedAtMonth'
                    || $field === 'updatedAtYear'
                    || isset($diffFieldsToSkip[$field])
                ) {
                    unset($entityData[$field]);
                    continue;
                }
                if ($field === 'password') {
                    $changes = [
                        'f' => '**********',
                        't' => '**********',
                    ];
                } else {
                    $changes = [
                        'f' => $this->serializeDiffValue($change[0]),
                        't' => $this->serializeDiffValue($change[1]),
                    ];
                }
                if ($isHistorizableStatus && $field === HistoryLogger::STATUS_PROPERTY) {
                    $statusDiff = $changes;
                    unset($entityData[$field]);
                    continue;
                }
                $entityData[$field] = $changes;
            }
            if ($isHistorizableStatus) {
                $historyData[HistoryLogger::STATUS_PROPERTY] = $statusDiff;
            }

            // MDT diff of ManyToMany collections, currently not possible to have the state of the collection before preUpdate
            $uow = $args->getObjectManager()->getUnitOfWork();
            $entityClass = get_class($entity);
            foreach ($uow->getScheduledCollectionUpdates() as $collection) {
                if ($entityClass !== get_class($collection->getOwner())) {
                    continue;
                }
                // MDT the 'c_u' string is a shortcut index key meaning collection_update
                $entityData[$collection->getMapping()['fieldName']]['c_u'] = $collection->map(function ($item) {
                    return $this->serializeDiffValue($item);
                })->toArray();
            }
            // MDT the preUpdate event is not triggered if the only modification of the event concerns the deletion of the collection
            // cf. https://github.com/doctrine/orm/issues/9960
            foreach ($uow->getScheduledCollectionDeletions() as $collection) {
                $entityData[$collection->getMapping()['fieldName']] = 'label.empty';
            }
            $historyData[HistoryLogger::DIFF_PROPERTY] = $entityData;
        }
        if (isset($historyData[HistoryLogger::DIFF_PROPERTY]['archivedAt'])) {
            $code = HistoryLogger::ARCHIVED_CODE;
            unset($historyData[HistoryLogger::DIFF_PROPERTY]['archivedAt']);
        }

        // If the $entityData is empty after parsing ChangeSet we remove the index to lower history data storage in the database
        if (empty($historyData[HistoryLogger::DIFF_PROPERTY])) {
            unset($historyData[HistoryLogger::DIFF_PROPERTY]);
        }

        if ($this->historyExtraData !== null) {
            $historyData = array_merge($historyData, $this->historyExtraData);
        }

        // No need for setFlushLog because it already takes place after the prePersist/preUpdate event doctrine
        $this->historyLogger->log($entity, $code, $historyData);
    }

    private function serializeDiffValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            if ($value->format(PhpFormatter::DATE_FR) === '01/01/1970') {
                return $value->format('H\hi');
            }

            return $value->format(\DateTimeInterface::ATOM);
        } elseif ($value instanceof \Stringable) {
            return (string) $value;
        }

        return $value;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function setPrePersistCode(string $prePersistCode): void
    {
        $this->prePersistCode = $prePersistCode;
    }

    public function addHistoryExtraData(?array $historyExtraData): void
    {
        $this->historyExtraData = $historyExtraData;
    }
}
