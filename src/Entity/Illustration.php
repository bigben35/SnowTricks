<?php

namespace App\Entity;

use App\Entity\Trick;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\IllustrationRepository;

#[ORM\Entity(repositoryClass: IllustrationRepository::class)]

class Illustration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'illustrations')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Trick $trick = null;


    // convertir tableau en chaine de caractères 
    public function __toString()
    {
        return $this->file;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

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
}
