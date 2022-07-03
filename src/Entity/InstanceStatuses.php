<?php

namespace App\Entity;

use App\Repository\InstanceStatusesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstanceStatusesRepository::class)]
class InstanceStatuses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Instances::class, orphanRemoval: true)]
    private $instances;

    public function __construct()
    {
        $this->instances = new ArrayCollection();
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
     * @return Collection<int, Instances>
     */
    public function getInstances(): Collection
    {
        return $this->instances;
    }

    public function addInstance(Instances $instance): self
    {
        if (!$this->instances->contains($instance)) {
            $this->instances[] = $instance;
            $instance->setStatus($this);
        }

        return $this;
    }

    public function removeInstance(Instances $instance): self
    {
        if ($this->instances->removeElement($instance)) {
            // set the owning side to null (unless already changed)
            if ($instance->getStatus() === $this) {
                $instance->setStatus(null);
            }
        }

        return $this;
    }
}
