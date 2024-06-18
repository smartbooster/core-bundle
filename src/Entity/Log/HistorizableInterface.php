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
}
