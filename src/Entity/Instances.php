<?php

namespace App\Entity;

use App\Repository\InstancesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Environments;
use App\Entity\InstanceTypes;
use App\Entity\InstanceStatuses;
use DateTimeImmutable;
use App\Entity\Addresses;

#[ORM\Entity(repositoryClass: InstancesRepository::class)]
#[ORM\UniqueConstraint(name: "instances_name", columns: ["name"])]
class Instances
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $created_at;

    #[ORM\ManyToOne(targetEntity: InstanceTypes::class, inversedBy: 'instances')]
    #[ORM\JoinColumn(nullable: false)]
    private InstanceTypes $instance_type;

    #[ORM\OneToOne(mappedBy: 'instance', targetEntity: Environments::class, cascade: ['persist', 'remove'])]
    private ?Environments $envs;

    #[ORM\ManyToOne(targetEntity: InstanceStatuses::class, inversedBy: 'instances')]
    #[ORM\JoinColumn(nullable: false, options: ['default' => 6])]
    private InstanceStatuses $status;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * 
     * @var Collection<int, Addresses>
     */
    #[ORM\OneToMany(mappedBy: 'instance', targetEntity: Addresses::class)]
    private $addresses; 
        
#    private $addressesCounter;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();

        $now = new \DateTimeImmutable('NOW');
	$this->created_at = $now;
//        $instance->setCreatedAt($now);
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getInstanceType(): InstanceTypes
    {
        return $this->instance_type;
    }

    public function setInstanceType(?InstanceTypes $instance_type): self {
        if ($instance_type) {
            $this->instance_type = $instance_type;
        }
        return $this;
    }

    public function getEnvs(): ?Environments
    {
        return $this->envs;
    }

    public function setEnvs(?Environments $envs): self
    {
        // unset the owning side of the relation if necessary
        if ($envs === null && $this->envs !== null) {
            $this->envs->setInstance(null);
        }

        // set the owning side of the relation if necessary
        if ($envs !== null && $envs->getInstance() !== $this) {
            $envs->setInstance($this);
        }

        $this->envs = $envs;

        return $this;
    }

    public function getStatus(): InstanceStatuses
    {
        return $this->status;
    }

    public function setStatus(InstanceStatuses $status): self {
        $this->status = $status;
        return $this;
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

    /**
     * @return Collection<int, Addresses>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Addresses $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setInstance($this);
        }

        return $this;
    }

    public function removeAddress(Addresses $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getInstance() === $this) {
                $address->setInstance(null);
            }
        }

        return $this;
    }

    public function getAddressesCounter(): int
    {
        return count( $this->getAddresses());
    }
    
    public function getEnvHash(): string
    {
        return $this->getEnvs()?$this->getEnvs()->getHash():"";
    }
}
