<?php

namespace App\Entity;

use App\Repository\OperatingSystemsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OperatingSystemsRepository::class)]
class OperatingSystems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'simple_array')]
    private $breed = [];

    #[ORM\Column(type: 'string', length: 255)]
    private $release;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private $supported;

    #[ORM\OneToMany(mappedBy: 'os', targetEntity: InstanceTypes::class, orphanRemoval: true)]
    private $instanceTypes;

    #[ORM\OneToMany(mappedBy: 'os', targetEntity: SessionOses::class, orphanRemoval: true)]
    private $sessions;

    #[ORM\OneToMany(mappedBy: 'os', targetEntity: TaskOses::class, orphanRemoval: true)]
    private $osTasks;

    public function __construct()
    {
        $this->instanceTypes = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->osTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBreed(): ?array
    {
        return $this->breed;
    }

    public function setBreed(array $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getRelease(): ?string
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

    public function isSupported(): ?bool
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
        return $this->sessions;
    }

    public function addSession(SessionOses $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setOs($this);
        }

        return $this;
    }

    public function removeSession(SessionOses $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getOs() === $this) {
                $session->setOs(null);
            }
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
            if ($osTask->getOs() === $this) {
                $osTask->setOs(null);
            }
        }

        return $this;
    }

}
