<?php

namespace App\Entity;

use App\Repository\SessionOsesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sessions;
use App\Entity\OperatingSystems;

#[ORM\Entity(repositoryClass: SessionOsesRepository::class)]
#[ORM\UniqueConstraint(name: "session_oses_combo", columns: ["session_id", "os_id"])]
class SessionOses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Sessions::class, inversedBy: 'sessionOses')]
    #[ORM\JoinColumn(nullable: false)]
    private Sessions $session;

    #[ORM\ManyToOne(targetEntity: OperatingSystems::class, inversedBy: 'osSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private OperatingSystems $os;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Sessions
    {
        return $this->session;
    }

    public function setSession(?Sessions $session): self
    {
        $this->session = $session;

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
