<?php

namespace Smart\CoreBundle\Entity\Log;

/**
 * Allows you to have a dedicated display of status transitions in the history rows from the sonata show_history_field.html.twig template
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface HistorizableStatusInterface
{
    public function getStatus(): mixed;
}
