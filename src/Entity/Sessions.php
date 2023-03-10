<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
#[ORM\UniqueConstraint(name: "sessions_hash", columns: ["hash"])]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $started_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $finished_at;

    #[ORM\Column(type: 'string', length: 8)]
    private $hash;

    #[ORM\ManyToOne(targetEntity: Testees::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private $testee;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionOses::class, orphanRemoval: true)]
    private $sessionOses;

    private $osesCounter;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionTechs::class, orphanRemoval: true)]
    private $sessionTechs;

    private $techsCounter;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Environments::class)]
    private $envs;

    private $envsCounter;

    #[ORM\ManyToOne(targetEntity: SessionStatuses::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false, options: ['default' => 1])]
    private $status;

    public function __construct()
    {
        $this->sessionOses = new ArrayCollection();
        $this->sessionTechs = new ArrayCollection();
        $this->envs = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->getTestee()->getEmail()." at ".$this->getCreatedAt()->format('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getTestee(): ?Testees
    {
        return $this->testee;
    }

    public function setTestee(?Testees $testee): self
    {
        $this->testee = $testee;

        return $this;
    }

    /**
     * @return Collection<int, SessionOses>
     */
    public function getSessionOses(): Collection
    {
        return $this->sessionOses;
    }

    public function addOs(SessionOses $os): self
    {
        if (!$this->sessionOses->contains($os)) {
            $this->sessionOses[] = $os;
            $os->setSession($this);
        }

        return $this;
    }

    public function removeOs(SessionOses $os): self
    {
        if ($this->sessionOses->removeElement($os)) {
            // set the owning side to null (unless already changed)
            if ($os->getSession() === $this) {
                $os->setSession(null);
            }
        }

        return $this;
    }

    public function getOsesCounter(): int
    {
        return $this->osesCounter = count( $this->getSessionOses());
    }

    /**
     * @return Collection<int, SessionTechs>
     */
    public function getSessionTechs(): Collection
    {
        return $this->sessionTechs;
    }

    public function addSessionTech(SessionTechs $sessionTech): self
    {
        if (!$this->sessionTechs->contains($sessionTech)) {
            $this->sessionTechs[] = $sessionTech;
            $sessionTech->setSession($this);
        }

        return $this;
    }

    public function removeSessionTech(SessionTechs $sessionTech): self
    {
        if ($this->sessionTechs->removeElement($sessionTech)) {
            // set the owning side to null (unless already changed)
            if ($sessionTech->getSession() === $this) {
                $sessionTech->setSession(null);
            }
        }

        return $this;
    }

    public function getTechsCounter(): int
    {
        return $this->techsCounter = count( $this->getSessionTechs());
    }

    /**
     * @return Collection<int, Environments>
     */
    public function getEnvs(): Collection
    {
        return $this->envs;
    }

    public function getEnvsCounter(): int
    {
        return $this->envsCounter = count( $this->getEnvs());
    }

    public function addEnv(Environments $env): self
    {
        if (!$this->envs->contains($env)) {
            $this->envs[] = $env;
            $env->setSession($this);
        }

        return $this;
    }

    public function removeEnv(Environments $env): self
    {
        if ($this->envs->removeElement($env)) {
            // set the owning side to null (unless already changed)
            if ($env->getSession() === $this) {
                $env->setSession(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?SessionStatuses
    {
        return $this->status;
    }

    public function setStatus(?SessionStatuses $status): self
    {
        $this->status = $status;

        return $this;
    }

}
