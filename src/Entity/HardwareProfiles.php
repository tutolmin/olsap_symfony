<?php

namespace App\Entity;

use App\Repository\HardwareProfilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HardwareProfilesRepository::class)]
class HardwareProfiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'hw_profile', targetEntity: InstanceTypes::class, orphanRemoval: true)]
    private $instanceTypes;

    public function __construct()
    {
        $this->instanceTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, InstanceTypes>
     */
    public function getInstanceTypes(): Collection
    {
        return $this->instanceTypes;
    }

    public function addInstanceType(InstanceTypes $instanceType): self
    {
        if (!$this->instanceTypes->contains($instanceType)) {
            $this->instanceTypes[] = $instanceType;
            $instanceType->setHwProfile($this);
        }

        return $this;
    }

    public function removeInstanceType(InstanceTypes $instanceType): self
    {
        if ($this->instanceTypes->removeElement($instanceType)) {
            // set the owning side to null (unless already changed)
            if ($instanceType->getHwProfile() === $this) {
                $instanceType->setHwProfile(null);
            }
        }

        return $this;
    }
}
