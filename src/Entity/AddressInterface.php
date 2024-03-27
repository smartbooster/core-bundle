<?php

namespace Smart\CoreBundle\Entity;

interface AddressInterface
{
    public function getAddress(): ?string;

    public function getAdditionalAddress(): ?string;

    public function getPostalCode(): ?string;

    public function getCity(): ?string;
}
