<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Trick;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentTrickRepository;

#[ORM\Entity(repositoryClass: CommentTrickRepository::class)]
class CommentTrick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'commentTricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trick = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commentTricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $connectedUser = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getConnectedUser(): ?User
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?User $connectedUser): self
    {
        $this->connectedUser = $connectedUser;

        return $this;
    }
}
