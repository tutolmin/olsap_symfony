<?php

namespace App\Entity;

use App\Repository\SessionStatusesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sessions;

#[ORM\Entity(repositoryClass: SessionStatusesRepository::class)]
#[ORM\UniqueConstraint(name: "session_statuses_status", columns: ["status"])]
class SessionStatuses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status;

    /**
     * 
     * @var Collection<int, Sessions>
     */
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Sessions::class, orphanRemoval: true)]
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Sessions>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Sessions $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setStatus($this);
        }

        return $this;
    }

    public function removeSession(Sessions $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getStatus() === $this) {
                $session->setStatus(null);
            }
        }

        return $this;
    }
}
