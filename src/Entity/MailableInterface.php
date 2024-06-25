<?php

namespace Smart\CoreBundle\Entity;

/**
 * Allows you to identify entities with email sending to simplify the Mailer::setRecipientToEmail
 *
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface MailableInterface extends \Stringable
{
    /**
     * Could also contain multiple emails in the form of a string separated by a comma
     * MDT deliberately does not call the method getEmail() so as not to overlap on the generic getter getEmail thus
     * forcing developers to implement the interface to define the getter logic.
     *  For example : getEmail() ?? getEmail2()
     */
    public function getRecipientEmail(): ?string;
    public function getCc(): ?string;
    public function getCci(): ?string;
}
