<?php

namespace App\Entity;

use App\Repository\HardwareProfilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\InstanceTypes;

#[ORM\Entity(repositoryClass: HardwareProfilesRepository::class)]
#[ORM\UniqueConstraint(name: "hardware_profiles_name", columns: ["name"])]
class HardwareProfiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'boolean')]
    private bool $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    /**
     * 
     * @var Collection<int, InstanceTypes>
     */
    #[ORM\OneToMany(mappedBy: 'hw_profile', targetEntity: InstanceTypes::class, orphanRemoval: true)]
    private $instanceTypes;

    #[ORM\Column(type: 'integer')]
    private int $cost;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(options: ['default' => false])]
    private bool $supported = false;

    public function __construct()
    {
        $this->instanceTypes = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
	return $this->getDescription();
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

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isSupported(): ?bool
    {
        return $this->supported;
    }

    public function setSupported(bool $supported): self
    {
        $this->supported = $supported;

        return $this;
    }
}
