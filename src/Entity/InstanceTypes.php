<?php

namespace App\Entity;

use App\Repository\InstanceTypesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstanceTypesRepository::class)]
class InstanceTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: OperatingSystems::class, inversedBy: 'instanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private $os;

    #[ORM\ManyToOne(targetEntity: HardwareProfiles::class, inversedBy: 'instanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private $hw_profile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOs(): ?OperatingSystems
    {
        return $this->os;
    }

    public function setOs(?OperatingSystems $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getHwProfile(): ?HardwareProfiles
    {
        return $this->hw_profile;
    }

    public function setHwProfile(?HardwareProfiles $hw_profile): self
    {
        $this->hw_profile = $hw_profile;

        return $this;
    }
}
