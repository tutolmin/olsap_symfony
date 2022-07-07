<?php

namespace App\Entity;

use App\Repository\TaskOsesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskOsesRepository::class)]
#[ORM\UniqueConstraint(name: "task_oses_combo", columns: ["task_id", "os_id"])]
class TaskOses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'taskOses')]
    #[ORM\JoinColumn(nullable: false)]
    private $task;

    #[ORM\ManyToOne(targetEntity: OperatingSystems::class, inversedBy: 'osTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $os;

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

    public function getOs(): ?OperatingSystems
    {
        return $this->os;
    }

    public function setOs(?OperatingSystems $os): self
    {
        $this->os = $os;

        return $this;
    }
}
