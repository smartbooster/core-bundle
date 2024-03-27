<?php

namespace Smart\CoreBundle\Entity;

interface OrganizationInterface extends NameableInterface, SirenInterface, AddressInterface, PhoneInterface
{
    public function getId(): ?int;

    public function getOrganizationEmail(): ?string;

    public function setOrganizationEmail(?string $organizationEmail): static;
}
