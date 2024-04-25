<?php

namespace App\Entity;

use App\Repository\TesteesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sessions;
use DateTimeImmutable;
#use Symfony\Component\Serializer\Annotation\Context;
#use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: TesteesRepository::class)]
#[ORM\UniqueConstraint(name: "testees_oauth_token", columns: ["oauth_token"])]
class Testees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $oauthToken;

    #[ORM\Column(type: 'datetime_immutable', options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeImmutable $registeredAt;

    /**
     * 
     * @var Collection<int, Sessions>
     */
    #[ORM\OneToMany(mappedBy: 'testee', targetEntity: Sessions::class, orphanRemoval: true)]
    private $sessions;
    
#    private $sessionsCounter;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    // https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5
    public function __toString() {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOauthToken(): string
    {
        return $this->oauthToken;
    }

    public function setOauthToken(string $oauth_token): self
    {
        $this->oauthToken = $oauth_token;

        return $this;
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registered_at): self
    {
        $this->registeredAt = $registered_at;

        return $this;
    }
    
    public function getSessionsCounter(): int
    {
        return count( $this->getSessions());
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
            $session->setTestee($this);
        }

        return $this;
    }

    public function removeSession(Sessions $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getTestee() === $this) {
                $session->setTestee(null);
            }
        }

        return $this;
    }
}
