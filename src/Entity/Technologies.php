<?php

namespace App\Entity;

use App\Repository\TechnologiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Domains;
use App\Entity\SessionTechs;
use App\Entity\TaskTechs;

#[ORM\Entity(repositoryClass: TechnologiesRepository::class)]
#[ORM\UniqueConstraint(name: "technologies_name", columns: ["name"])]
#[ORM\Index(name: "technologies_domain", columns: ["domain_id"])]
class Technologies
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Domains::class, inversedBy: 'technologies')]
    #[ORM\JoinColumn(nullable: false)]
    private Domains $domain;

    /**
     * 
     * @var Collection<int, SessionTechs>
     */
    #[ORM\OneToMany(mappedBy: 'tech', targetEntity: SessionTechs::class, orphanRemoval: true)]
    private $techSessions;

    /**
     * 
     * @var Collection<int, TaskTechs>
     */
    #[ORM\OneToMany(mappedBy: 'tech', targetEntity: TaskTechs::class, orphanRemoval: true)]
    private $techTasks;

#    private $tasksCounter;

    public function __construct()
    {
        $this->techSessions = new ArrayCollection();
        $this->techTasks = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
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

    public function getDomain(): Domains
    {
        return $this->domain;
    }

    public function setDomain(Domains $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection<int, SessionTechs>
     */
    public function getTechSessions(): Collection
    {
        return $this->techSessions;
    }

    public function addTechSession(SessionTechs $techSession): self
    {
        if (!$this->techSessions->contains($techSession)) {
            $this->techSessions[] = $techSession;
            $techSession->setTech($this);
        }

        return $this;
    }

    public function removeTechSession(SessionTechs $techSession): self
    {
        if ($this->techSessions->removeElement($techSession)) {
            // set the owning side to null (unless already changed)
            /*
            if ($techSession->getTech() === $this) {
                $techSession->setTech(null);
            }
             * 
             */
        }

        return $this;
    }

    /**
     * @return Collection<int, TaskTechs>
     */
    public function getTechTasks(): Collection
    {
        return $this->techTasks;
    }

    public function addTechTask(TaskTechs $techTask): self
    {
        if (!$this->techTasks->contains($techTask)) {
            $this->techTasks[] = $techTask;
            $techTask->setTech($this);
        }

        return $this;
    }

    public function removeTechTask(TaskTechs $techTask): self
    {
        if ($this->techTasks->removeElement($techTask)) {
            // set the owning side to null (unless already changed)
            /*
            if ($techTask->getTech() === $this) {
                $techTask->setTech(null);
            }
             *
             */
        }

        return $this;
    }
    
    public function getSessionsCounter(): int
    {   
        return count( $this->getTechSessions());
    }
    
    public function getTasksCounter(): int
    {   
        return count( $this->getTechTasks());
    }
}
