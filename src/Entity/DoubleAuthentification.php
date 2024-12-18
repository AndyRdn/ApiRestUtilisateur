<?php

namespace App\Entity;

use App\Repository\DoubleAuthentificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoubleAuthentificationRepository::class)]
class DoubleAuthentification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'code')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column]
    private ?int $code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $daty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDaty(): ?\DateTimeImmutable
    {
        return $this->daty;
    }

    public function setDaty(\DateTimeImmutable $daty): static
    {
        $this->daty = $daty;

        return $this;
    }
}
