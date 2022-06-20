<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'simple_array')]
    private $status = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $ended_at;

    #[ORM\Column(type: 'string', length: 255)]
    private $hash;

    #[ORM\ManyToOne(targetEntity: Testee::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private $testee;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionOses::class, orphanRemoval: true)]
    private $oses;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionTechs::class, orphanRemoval: true)]
    private $sessionTechs;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Environments::class)]
    private $envs;

    public function __construct()
    {
        $this->oses = new ArrayCollection();
        $this->sessionTechs = new ArrayCollection();
        $this->envs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }

    public function setStatus(array $status): self
    {
        $this->status = $status;

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

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->ended_at;
    }

    public function setEndedAt(?\DateTimeImmutable $ended_at): self
    {
        $this->ended_at = $ended_at;

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

    public function getTestee(): ?Testee
    {
        return $this->testee;
    }

    public function setTestee(?Testee $testee): self
    {
        $this->testee = $testee;

        return $this;
    }

    /**
     * @return Collection<int, SessionOses>
     */
    public function getOses(): Collection
    {
        return $this->oses;
    }

    public function addOs(SessionOses $os): self
    {
        if (!$this->sessionOses->contains($os)) {
            $this->oses[] = $os;
            $os->setSession($this);
        }

        return $this;
    }

    public function removeOs(SessionOses $os): self
    {
        if ($this->oses->removeElement($os)) {
            // set the owning side to null (unless already changed)
            if ($os->getSession() === $this) {
                $os->setSession(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, Environments>
     */
    public function getEnvs(): Collection
    {
        return $this->envs;
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
}
