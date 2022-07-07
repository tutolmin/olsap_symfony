<?php

namespace App\Entity;

use App\Repository\DomainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainsRepository::class)]
#[ORM\UniqueConstraint(name: "domains_name", columns: ["name"])]
class Domains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: Technologies::class, orphanRemoval: true)]
    private $technologies;

    private $techsCounter;

    public function __construct()
    {
        $this->technologies = new ArrayCollection();
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
     * @return Collection<int, Technologies>
     */
    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function addTechnology(Technologies $technology): self
    {
        if (!$this->technologies->contains($technology)) {
            $this->technologies[] = $technology;
            $technology->setDomain($this);
        }

        return $this;
    }

    public function removeTechnology(Technologies $technology): self
    {
        if ($this->technologies->removeElement($technology)) {
            // set the owning side to null (unless already changed)
            if ($technology->getDomain() === $this) {
                $technology->setDomain(null);
            }
        }

        return $this;
    }

    public function getTechsCounter(): int
    {
	return $this->techsCounter = count( $this->getTechnologies());
    }
}
