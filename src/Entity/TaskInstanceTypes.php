<?php

namespace App\Entity;

use App\Repository\TaskInstanceTypesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Tasks;
use App\Entity\InstanceTypes;

#[ORM\Entity(repositoryClass: TaskInstanceTypesRepository::class)]
#[ORM\UniqueConstraint(name: "task_instance_types_combo", columns: ["task_id", "instance_type_id"])]
class TaskInstanceTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'taskInstanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private Tasks $task;

    #[ORM\ManyToOne(targetEntity: InstanceTypes::class, inversedBy: 'instanceTypeTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private InstanceTypes $instance_type;

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

    public function getInstanceType(): InstanceTypes
    {
        return $this->instance_type;
    }

    public function setInstanceType(InstanceTypes $instance_type): self
    {
        $this->instance_type = $instance_type;

        return $this;
    }
}
