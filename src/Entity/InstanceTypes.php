<?php

namespace App\Entity;

use App\Repository\InstanceTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;
use App\Entity\Instances;
use App\Entity\TaskInstanceTypes;

#[ORM\Entity(repositoryClass: InstanceTypesRepository::class)]
#[ORM\UniqueConstraint(name: "instance_types_combo", columns: ["hw_profile_id", "os_id"])]
class InstanceTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: OperatingSystems::class, inversedBy: 'instanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private OperatingSystems $os;

    #[ORM\ManyToOne(targetEntity: HardwareProfiles::class, inversedBy: 'instanceTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private HardwareProfiles $hw_profile;

#    private $combo;
    /**
     * 
     * @var Collection<int, Instances>
     */
    #[ORM\OneToMany(mappedBy: 'instance_type', targetEntity: Instances::class, orphanRemoval: true)]
    private $instances;

#    private $instancesCounter;

    /**
     * 
     * @var Collection<int, TaskInstanceTypes>
     */
    #[ORM\OneToMany(mappedBy: 'instance_type', targetEntity: TaskInstanceTypes::class, orphanRemoval: true)]
    private $instanceTypeTasks;

    public function __construct()
    {
        $this->instances = new ArrayCollection();
        $this->instanceTypeTasks = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->getOs()." @ ".$this->getHwProfile();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOs(): OperatingSystems
    {
        return $this->os;
    }

    public function setOs(?OperatingSystems $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getHwProfile(): HardwareProfiles
    {
        return $this->hw_profile;
    }

    public function setHwProfile(?HardwareProfiles $hw_profile): self
    {
        $this->hw_profile = $hw_profile;

        return $this;
    }

    public function getCombo(): string
    {
        return $this->os->getAlias() . "@" . $this->hw_profile->getName();
    }

    /**
     * @return Collection<int, Instances>
     */
    public function getInstances(): Collection
    {
        return $this->instances;
    }

    public function getInstancesCounter(): int
    {
        return count( $this->getInstances());
    }

    public function addInstance(Instances $instance): self
    {
        if (!$this->instances->contains($instance)) {
            $this->instances[] = $instance;
            $instance->setInstanceType($this);
        }

        return $this;
    }

    public function removeInstance(Instances $instance): self
    {
        if ($this->instances->removeElement($instance)) {
            // set the owning side to null (unless already changed)
            if ($instance->getInstanceType() === $this) {
                $instance->setInstanceType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TaskInstanceTypes>
     */
    public function getInstanceTypeTasks(): Collection
    {
        return $this->instanceTypeTasks;
    }

    public function addInstanceTypeTask(TaskInstanceTypes $instanceTypeTask): self
    {
        if (!$this->instanceTypeTasks->contains($instanceTypeTask)) {
            $this->instanceTypeTasks[] = $instanceTypeTask;
            $instanceTypeTask->setInstanceType($this);
        }

        return $this;
    }

    public function removeInstanceTypeTask(TaskInstanceTypes $instanceTypeTask): self
    {
        if ($this->instanceTypeTasks->removeElement($instanceTypeTask)) {
            // set the owning side to null (unless already changed)
            if ($instanceTypeTask->getInstanceType() === $this) {
                $instanceTypeTask->setInstanceType(null);
            }
        }

        return $this;
    }
}
