<?php

namespace Smart\CoreBundle\Entity\User;

interface UserProfileInterface extends \Stringable
{
    public function getProfile(): string;
}
