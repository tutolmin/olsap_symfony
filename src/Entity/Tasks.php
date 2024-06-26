<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\TaskOses;
use App\Entity\TaskTechs;
use App\Entity\TaskInstanceTypes;
use App\Entity\Environments;

#[ORM\Entity(repositoryClass: TasksRepository::class)]
#[ORM\UniqueConstraint(name: "tasks_name", columns: ["name"])]
#[ORM\UniqueConstraint(name: 'tasks_project', columns: ['project'])]
#[ORM\UniqueConstraint(name: 'tasks_deploy', columns: ['deploy'])]
#[ORM\UniqueConstraint(name: 'tasks_solve', columns: ['solve'])]
#[ORM\UniqueConstraint(name: 'tasks_verify', columns: ['verify'])]
class Tasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 255)]
    private string $path;

    /**
     * 
     * @var Collection<int, TaskOses>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskOses::class, orphanRemoval: true)]
    private $taskOses;

#    private $osesCounter;

    /**
     * 
     * @var Collection<int, TaskTechs>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskTechs::class, orphanRemoval: true)]
    private $taskTechs;

#    private $techsCounter;

    /**
     * 
     * @var Collection<int, TaskInstanceTypes>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskInstanceTypes::class, orphanRemoval: true)]
    private $taskInstanceTypes;

#    private $instanceTypesCounter;

    /**
     * 
     * @var Collection<int, Environments>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Environments::class, orphanRemoval: true)]
    private $envs;

    #[ORM\Column(nullable: true)]
    private ?int $project = null;

    #[ORM\Column(nullable: true)]
    private ?int $solve = null;

    #[ORM\Column(nullable: true)]
    private ?int $deploy = null;

    #[ORM\Column(nullable: true)]
    private ?int $verify = null;

    public function __construct()
    {
        $this->taskOses = new ArrayCollection();
        $this->taskTechs = new ArrayCollection();
        $this->taskInstanceTypes = new ArrayCollection();
        $this->envs = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->getPath() . ": " . $this->name;
    }

    public function getId(): int
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Collection<int, TaskOses>
     */
    public function getTaskOses(): Collection
    {
        return $this->taskOses;
    }

    public function addTaskOse(TaskOses $taskOse): self
    {
        if (!$this->taskOses->contains($taskOse)) {
            $this->taskOses[] = $taskOse;
            $taskOse->setTask($this);
        }

        return $this;
    }

    public function removeTaskOse(TaskOses $taskOse): self
    {
        if ($this->taskOses->removeElement($taskOse)) {
            // set the owning side to null (unless already changed)
            /*
            if ($taskOse->getTask() === $this) {
                $taskOse->setTask(null);
            }
             * 
             */
        }

        return $this;
    }

    /**
     * @return Collection<int, TaskTechs>
     */
    public function getTaskTechs(): Collection
    {
        return $this->taskTechs;
    }

    public function addTaskTech(TaskTechs $taskTech): self
    {
        if (!$this->taskTechs->contains($taskTech)) {
            $this->taskTechs[] = $taskTech;
            $taskTech->setTask($this);
        }

        return $this;
    }

    public function removeTaskTech(TaskTechs $taskTech): self
    {
        if ($this->taskTechs->removeElement($taskTech)) {
            // set the owning side to null (unless already changed)
            /*
            if ($taskTech->getTask() === $this) {
                $taskTech->setTask(null);
            }
             * 
             */
        }

        return $this;
    }

    public function getTechsCounter(): int
    {
        return count( $this->getTaskTechs());
    }

    public function getOsesCounter(): int
    {
        return count( $this->getTaskOses());
    }

    public function getInstanceTypesCounter(): int
    {
        return count( $this->getTaskInstanceTypes());
    }

    /**
     * @return Collection<int, TaskInstanceTypes>
     */
    public function getTaskInstanceTypes(): Collection
    {
        return $this->taskInstanceTypes;
    }

    public function addTaskInstanceType(TaskInstanceTypes $taskInstanceType): self
    {
        if (!$this->taskInstanceTypes->contains($taskInstanceType)) {
            $this->taskInstanceTypes[] = $taskInstanceType;
            $taskInstanceType->setTask($this);
        }

        return $this;
    }

    public function removeTaskInstanceType(TaskInstanceTypes $taskInstanceType): self
    {
        if ($this->taskInstanceTypes->removeElement($taskInstanceType)) {
            // set the owning side to null (unless already changed)
            /*
            if ($taskInstanceType->getTask() === $this) {
                $taskInstanceType->setTask(null);
            }
             * 
             */
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
            $env->setTask($this);
        }

        return $this;
    }

    public function removeEnv(Environments $env): self
    {
        if ($this->envs->removeElement($env)) {
            // set the owning side to null (unless already changed)
            if ($env->getTask() === $this) {
                $env->setTask(null);
            }
        }

        return $this;
    }

    public function getProject(): ?int
    {
        return $this->project;
    }

    public function setProject(?int $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSolve(): ?int
    {
        return $this->solve;
    }

    public function setSolve(?int $solve): self
    {
        $this->solve = $solve;

        return $this;
    }

    public function getDeploy(): ?int
    {
        return $this->deploy;
    }

    public function setDeploy(?int $deploy): self
    {
        $this->deploy = $deploy;

        return $this;
    }

    public function getVerify(): ?int
    {
        return $this->verify;
    }

    public function setVerify(?int $verify): self
    {
        $this->verify = $verify;

        return $this;
    }
}
