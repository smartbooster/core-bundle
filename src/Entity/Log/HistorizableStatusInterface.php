<?php

namespace Smart\CoreBundle\Entity\Log;

/**
 * Permet d'avoir un affichage spécifique sur les transitions de status dans l'historique
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface HistorizableStatusInterface
{
    public function getStatus(): mixed;
}
