<?php

namespace App\Entity;

use App\Repository\SessionTechsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionTechsRepository::class)]
#[ORM\UniqueConstraint(name: "session_techs_combo", columns: ["session_id", "tech_id"])]
class SessionTechs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Sessions::class, inversedBy: 'sessionTechs')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    #[ORM\ManyToOne(targetEntity: Technologies::class, inversedBy: 'techSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private $tech;

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

    public function getTech(): ?Technologies
    {
        return $this->tech;
    }

    public function setTech(?Technologies $tech): self
    {
        $this->tech = $tech;

        return $this;
    }
}
