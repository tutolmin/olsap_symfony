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

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $finished_at;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $valid;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $deployment;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $verification;

    #[ORM\ManyToOne(targetEntity: EnvironmentStatuses::class, inversedBy: 'environments')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hash = null;

    public function __construct()
    {
#	parent::__construct();

        $timestamp = new \DateTimeImmutable('NOW');
        $this->hash = substr(md5($timestamp->format('Y-m-d H:i:s')),0,8);
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {

	$instance = $this->getInstance();

	if($instance)

          return $this->getTask() . " @ ". $this->getInstance() . ": ". $this->getStatus();
//        return strval($this->getId());

        return $this->getTask() . ": ". $this->getStatus();
    }

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

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finished_at;
    }

    public function setFinishedAt(?\DateTimeImmutable $finished_at): self
    {
        $this->finished_at = $finished_at;

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

    public function getVerification(): ?int
    {
        return $this->verification;
    }

    public function setVerification(?int $verification): self
    {
        $this->verification = $verification;

        return $this;
    }

    public function getDeployment(): ?int
    {
        return $this->deployment;
    }

    public function setDeployment(?int $deployment): self
    {
        $this->deployment = $deployment;

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

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }
}
