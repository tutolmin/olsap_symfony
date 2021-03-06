<?php

namespace App\Entity;

use App\Repository\EnvironmentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvironmentsRepository::class)]
class Environments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'envs')]
    #[ORM\JoinColumn(nullable: false)]
    private $task;

    #[ORM\ManyToOne(targetEntity: Sessions::class, inversedBy: 'envs')]
    private $session;

    #[ORM\OneToOne(inversedBy: 'envs', targetEntity: Instances::class, cascade: ['persist', 'remove'])]
    private $instance;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $started_at;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $valid;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $path;

    #[ORM\ManyToOne(targetEntity: EnvironmentStatuses::class, inversedBy: 'environments')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Tasks
    {
        return $this->task;
    }

    public function setTask(?Tasks $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getSession(): ?Sessions
    {
        return $this->session;
    }

    public function setSession(?Sessions $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getInstance(): ?Instances
    {
        return $this->instance;
    }

    public function setInstance(?Instances $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->started_at;
    }

    public function setStartedAt(?\DateTimeImmutable $started_at): self
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getStatus(): ?EnvironmentStatuses
    {
        return $this->status;
    }

    public function setStatus(?EnvironmentStatuses $status): self
    {
        $this->status = $status;

        return $this;
    }
}
