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
     * Permet d'activer ou non l'historique sur les events doctrine prePersist/preUpdate pour les diffs
     */
    public function isDoctrineListenerEnable(): bool;
}
