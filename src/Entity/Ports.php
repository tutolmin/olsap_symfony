<?php

namespace App\Entity;

use App\Repository\PortsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortsRepository::class)]
#[ORM\UniqueConstraint(name: 'ports_number', columns: ['number'])]
class Ports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\OneToOne(inversedBy: 'port', cascade: ['persist', 'remove'])]
    private ?Addresses $address = null;

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return strval($this->number);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAddress(): ?Addresses
    {
        return $this->address;
    }

    public function setAddress(?Addresses $address): self
    {
        $this->address = $address;

        return $this;
    }

}
