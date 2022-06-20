<?php

namespace App\Entity;

use App\Repository\TaskInstanceTypesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskInstanceTypesRepository::class)]
class TaskInstanceTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'taskInstanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private $task;

    #[ORM\ManyToOne(targetEntity: InstanceTypes::class, inversedBy: 'instanceTypeTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $instance_type;

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

    public function getInstanceType(): ?InstanceTypes
    {
        return $this->instance_type;
    }

    public function setInstanceType(?InstanceTypes $instance_type): self
    {
        $this->instance_type = $instance_type;

        return $this;
    }
}
