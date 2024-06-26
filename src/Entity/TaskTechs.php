<?php

namespace App\Entity;

use App\Repository\TaskTechsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Tasks;
use App\Entity\Technologies;

#[ORM\Entity(repositoryClass: TaskTechsRepository::class)]
#[ORM\UniqueConstraint(name: "task_techs_combo", columns: ["task_id", "tech_id"])]
class TaskTechs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'taskTechs')]
    #[ORM\JoinColumn(nullable: false)]
    private Tasks $task;

    #[ORM\ManyToOne(targetEntity: Technologies::class, inversedBy: 'techTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Technologies $tech;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTask(): Tasks
    {
        return $this->task;
    }

    public function setTask(Tasks $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getTech(): Technologies
    {
        return $this->tech;
    }

    public function setTech(Technologies $tech): self
    {
        $this->tech = $tech;

        return $this;
    }
}
