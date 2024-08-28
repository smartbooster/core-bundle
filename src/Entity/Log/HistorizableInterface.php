<?php

namespace Smart\CoreBundle\Entity\Log;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface HistorizableInterface
{
    public function getId(): ?int;

    public function getHistory(): ?array;

    public function setHistory(?array $history): self;

    public function addHistory(array $history): self;

    /**
     * Allows you to activate or not the history on prePersist/preUpdate doctrine events for diffs
     */
    public function isDoctrineListenerEnable(): bool;

    /**
     * @return array Indexed array which contains field to skip storing on the history JSON by the HistoryDoctrineListener when doing update
     *  Example :
     *  [
     *      "progressionData" => true,
     *  ]
     */
    public function getHistoryDiffFieldsToSkip(): array;
}
