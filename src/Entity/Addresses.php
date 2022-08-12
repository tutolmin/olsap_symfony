<?php

namespace App\Entity;

use App\Repository\AddressesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressesRepository::class)]
class Addresses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    private ?Instances $instance = null;

    #[ORM\OneToOne(mappedBy: 'address', cascade: ['persist', 'remove'])]
    private ?Ports $port = null;

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->ip;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getInstance(): ?Instances
    {
        return $this->instance;
    }

    public function setInstance(?Instances $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    public function getPort(): ?Ports
    {
        return $this->port;
    }

    public function setPort(?Ports $port): self
    {
        // unset the owning side of the relation if necessary
        if ($port === null && $this->port !== null) {
            $this->port->setAddress(null);
        }

        // set the owning side of the relation if necessary
        if ($port !== null && $port->getAddress() !== $this) {
            $port->setAddress($this);
        }

        $this->port = $port;

        return $this;
    }
}
