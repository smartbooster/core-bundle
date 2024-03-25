<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Entity\AddressTrait;
use Symfony\Component\Validator\Constraints as Assert;

trait OrganizationTrait
{
    use NameableTrait;
    use SirenTrait;
    use AddressTrait;
    use PhoneTrait;
    use WebsiteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $organizationEmail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganizationEmail(): ?string
    {
        return $this->organizationEmail;
    }

    public function setOrganizationEmail(?string $organizationEmail): static
    {
        $this->organizationEmail = $organizationEmail;

        return $this;
    }
}
