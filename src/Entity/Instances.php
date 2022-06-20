<?php

namespace App\Entity;

use App\Repository\InstancesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstancesRepository::class)]
class Instances
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'simple_array')]
    private $staus = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\Column(type: 'integer')]
    private $port;

    #[ORM\ManyToOne(targetEntity: InstanceTypes::class, inversedBy: 'instances')]
    #[ORM\JoinColumn(nullable: false)]
    private $instance_type;

    #[ORM\OneToOne(mappedBy: 'instance', targetEntity: Environments::class, cascade: ['persist', 'remove'])]
    private $envs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStaus(): ?array
    {
        return $this->staus;
    }

    public function setStaus(array $staus): self
    {
        $this->staus = $staus;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getInstanceType(): ?InstanceTypes
    {
        return $this->instance_type;
    }

    public function setInstanceType(?InstanceTypes $instance_type): self
    {
        $this->instance_type = $instance_type;

        return $this;
    }

    public function getEnvs(): ?Environments
    {
        return $this->envs;
    }

    public function setEnvs(?Environments $envs): self
    {
        // unset the owning side of the relation if necessary
        if ($envs === null && $this->envs !== null) {
            $this->envs->setInstance(null);
        }

        // set the owning side of the relation if necessary
        if ($envs !== null && $envs->getInstance() !== $this) {
            $envs->setInstance($this);
        }

        $this->envs = $envs;

        return $this;
    }
}
