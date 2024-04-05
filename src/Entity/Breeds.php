<?php

namespace App\Entity;

use App\Repository\BreedsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BreedsRepository::class)]
#[ORM\UniqueConstraint(name: "breeds_name", columns: ["name"])]
class Breeds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'breed', targetEntity: OperatingSystems::class, orphanRemoval: true)]
    private $operatingSystems;

    public function __construct()
    {
        $this->operatingSystems = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, OperatingSystems>
     */
    public function getOperatingSystems(): Collection
    {
        return $this->operatingSystems;
    }

    public function addOperatingSystem(OperatingSystems $operatingSystem): self
    {
        if (!$this->operatingSystems->contains($operatingSystem)) {
            $this->operatingSystems[] = $operatingSystem;
            $operatingSystem->setBreed($this);
        }

        return $this;
    }

    public function removeOperatingSystem(OperatingSystems $operatingSystem): self
    {
        if ($this->operatingSystems->removeElement($operatingSystem)) {
            // set the owning side to null (unless already changed)
            if ($operatingSystem->getBreed() === $this) {
                $operatingSystem->setBreed(null);
            }
        }

        return $this;
    }
}
