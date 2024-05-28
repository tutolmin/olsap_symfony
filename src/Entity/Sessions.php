<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use App\Entity\Testees;
use App\Entity\SessionStatuses;
use App\Entity\SessionOses;
use App\Entity\SessionTechs;
use App\Entity\Environments;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
#[ORM\UniqueConstraint(name: "sessions_hash", columns: ["hash"])]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $started_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $finished_at;

    #[ORM\Column(type: 'string', length: 8)]
    private string $hash;

    #[ORM\ManyToOne(targetEntity: Testees::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private Testees $testee;

    /**
     * 
     * @var Collection<int, SessionOses>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionOses::class, orphanRemoval: true)]
    private $sessionOses;

#    private $osesCounter;

    /**
     * 
     * @var Collection<int, SessionTechs>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionTechs::class, orphanRemoval: true)]
    private $sessionTechs;

#    private $techsCounter;

    /**
     * 
     * @var Collection<int, Environments>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Environments::class)]
    private $envs;

#    private $envsCounter;

    #[ORM\ManyToOne(targetEntity: SessionStatuses::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false, options: ['default' => 1])]
    private SessionStatuses $status;

    public function __construct()
    {
        $this->sessionOses = new ArrayCollection();
        $this->sessionTechs = new ArrayCollection();
        $this->envs = new ArrayCollection();
        
        $timestamp = new \DateTimeImmutable('NOW');
        $this->hash = substr(md5($timestamp->format('Y-m-d H:i:s')),0,8);
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->getTestee()->getEmail()." at ".$this->getCreatedAt()->format('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
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

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getTestee(): Testees
    {
        return $this->testee;
    }

    public function setTestee(?Testees $testee): self
    {
        if($testee){
            $this->testee = $testee;
        }
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
/*
    public function removeOs(SessionOses $session_os): self
    {
        if ($this->sessionOses->removeElement($session_os)) {
            // set the owning side to null (unless already changed)
            if ($session_os->getSession() === $this) {
                $session_os->setSession(null);
            }
        }

        return $this;
    }
*/
    public function getOsesCounter(): int
    {
        return count( $this->getSessionOses());
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
            /*
            if ($sessionTech->getSession() === $this) {
                $sessionTech->setSession(null);
            }
             * 
             */
        }

        return $this;
    }

    public function getTechsCounter(): int
    {
        return count( $this->getSessionTechs());
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
        return count( $this->getEnvs());
    }

    public function allocateEnvironment(Environments $env): self
    {
        if (!$this->envs->contains($env)) {
            $this->envs[] = $env;
            $env->setSession($this);
        }

        return $this;
    }

    public function releaseEnvironment(Environments $env): self
    {
        if ($this->envs->removeElement($env)) {
            // set the owning side to null (unless already changed)
            if ($env->getSession() === $this) {
                $env->setSession(null);
            }
        }

        return $this;
    }

    public function getStatus(): SessionStatuses
    {
        return $this->status;
    }

    public function setStatus(?SessionStatuses $status): self
    {
        if($status){
            $this->status = $status;
        }
        return $this;
    }

}
