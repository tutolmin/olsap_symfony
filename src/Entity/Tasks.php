<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksRepository::class)]
class Tasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $path;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskOses::class, orphanRemoval: true)]
    private $taskOses;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskTechs::class, orphanRemoval: true)]
    private $taskTechs;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskInstanceTypes::class, orphanRemoval: true)]
    private $taskInstanceTypes;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Environments::class, orphanRemoval: true)]
    private $envs;

    public function __construct()
    {
        $this->taskOses = new ArrayCollection();
        $this->taskTechs = new ArrayCollection();
        $this->taskInstanceTypes = new ArrayCollection();
        $this->envs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
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

    public function getPath(): ?string
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
            if ($taskOse->getTask() === $this) {
                $taskOse->setTask(null);
            }
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
            if ($taskTech->getTask() === $this) {
                $taskTech->setTask(null);
            }
        }

        return $this;
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
            if ($taskInstanceType->getTask() === $this) {
                $taskInstanceType->setTask(null);
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
}
