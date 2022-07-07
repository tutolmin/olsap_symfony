<?php

namespace App\Entity;

use App\Repository\EnvironmentStatusesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvironmentStatusesRepository::class)]
#[ORM\UniqueConstraint(name: "environments_statuses_status", columns: ["status"])]
class EnvironmentStatuses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Environments::class, orphanRemoval: true)]
    private $environments;

    public function __construct()
    {
        $this->environments = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Environments>
     */
    public function getEnvironments(): Collection
    {
        return $this->environments;
    }

    public function addEnvironment(Environments $environment): self
    {
        if (!$this->environments->contains($environment)) {
            $this->environments[] = $environment;
            $environment->setStatus($this);
        }

        return $this;
    }

    public function removeEnvironment(Environments $environment): self
    {
        if ($this->environments->removeElement($environment)) {
            // set the owning side to null (unless already changed)
            if ($environment->getStatus() === $this) {
                $environment->setStatus(null);
            }
        }

        return $this;
    }
}
