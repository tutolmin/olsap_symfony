<?php

namespace App\Entity;

use App\Repository\OperatingSystemsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Breeds;
use App\Entity\InstanceTypes;
use App\Entity\SessionOses;
use App\Entity\TaskOses;

#[ORM\Entity(repositoryClass: OperatingSystemsRepository::class)]
#[ORM\UniqueConstraint(name: "operating_systems_combo", columns: ["breed_id", "release"])]
#[ORM\Index(name: 'operating_systems_supported', columns: ['supported'])]
#[ORM\Index(name: 'operating_systems_breed', columns: ['breed_id'])]
class OperatingSystems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $release;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $supported = false;

    /**
     * 
     * @var Collection<int, InstanceTypes>
     */
    #[ORM\OneToMany(mappedBy: 'os', targetEntity: InstanceTypes::class, orphanRemoval: true)]
    private $instanceTypes;

    /**
     * 
     * @var Collection<int, SessionOses>
     */
    #[ORM\OneToMany(mappedBy: 'os', targetEntity: SessionOses::class, orphanRemoval: true)]
    private $osSessions;
#    private $sessionsCounter;

    /**
     * 
     * @var Collection<int, TaskOses>
     */
    #[ORM\OneToMany(mappedBy: 'os', targetEntity: TaskOses::class, orphanRemoval: true)]
    private $osTasks;

    #[ORM\ManyToOne(targetEntity: Breeds::class, inversedBy: 'operatingSystems')]
    #[ORM\JoinColumn(nullable: false)]
    private Breeds $breed;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $alias;

    public function __construct()
    {
        $this->instanceTypes = new ArrayCollection();
        $this->osSessions = new ArrayCollection();
        $this->osTasks = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->getBreed()." ".$this->release;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelease(): string
    {
        return $this->release;
    }

    public function setRelease(string $release): self
    {
        $this->release = $release;

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

    public function isSupported(): bool
    {
        return $this->supported;
    }

    public function setSupported(bool $supported): self
    {
        $this->supported = $supported;

        return $this;
    }

    /**
     * @return Collection<int, InstanceTypes>
     */
    public function getInstanceTypes(): Collection
    {
        return $this->instanceTypes;
    }

    public function addInstanceType(InstanceTypes $instanceType): self
    {
        if (!$this->instanceTypes->contains($instanceType)) {
            $this->instanceTypes[] = $instanceType;
            $instanceType->setOs($this);
        }

        return $this;
    }

    public function removeInstanceType(InstanceTypes $instanceType): self
    {
        if ($this->instanceTypes->removeElement($instanceType)) {
            // set the owning side to null (unless already changed)
            if ($instanceType->getOs() === $this) {
                $instanceType->setOs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SessionOses>
     */
    public function getSessions(): Collection
    {
        return $this->osSessions;
    }

    public function addSession(SessionOses $session): self
    {
        if (!$this->osSessions->contains($session)) {
            $this->osSessions[] = $session;
            $session->setOs($this);
        }

        return $this;
    }


    public function getSessionsCounter(): int
    {
        return count( $this->getSessions());
    }

    public function removeSession(SessionOses $session): self
    {
        if ($this->osSessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            /*
            if ($session->getOs() === $this) {
                $session->setOs(null);
            }
             * 
             */
        }

        return $this;
    }

    /**
     * @return Collection<int, TaskOses>
     */
    public function getOsTasks(): Collection
    {
        return $this->osTasks;
    }

    public function addOsTask(TaskOses $osTask): self
    {
        if (!$this->osTasks->contains($osTask)) {
            $this->osTasks[] = $osTask;
            $osTask->setOs($this);
        }

        return $this;
    }

    public function removeOsTask(TaskOses $osTask): self
    {
        if ($this->osTasks->removeElement($osTask)) {
            // set the owning side to null (unless already changed)
            /*
            if ($osTask->getOs() === $this) {
                $osTask->setOs(null);
            }
             * 
             */
        }

        return $this;
    }

    public function getBreed(): Breeds
    {
        return $this->breed;
    }

    public function setBreed(Breeds $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

}
