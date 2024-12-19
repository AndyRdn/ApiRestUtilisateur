<?php

namespace App\Entity;

use App\Repository\HistoriqueUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;


#[ORM\Entity(repositoryClass: HistoriqueUtilisateurRepository::class)]
class HistoriqueUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateNaissance = null;

    #[ORM\Column]
    private ?int $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeImmutable
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeImmutable $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getGenre(): ?int
    {
        return $this->genre;
    }

    public function setGenre(int $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function makeFromUser(Utilisateur $utilisateur, DateTimeImmutable $date)
    {
        $this->setId($utilisateur->getId());
        $this->prenom = $utilisateur->getPrenom();
        $this->nom = $utilisateur->getNom();
        $this->dateNaissance = $utilisateur->getDateNaissance();
        $this->genre = $utilisateur->getGenre();
        $this->motDePasse = $utilisateur->getMotDePasse();
        $this->updatedAt = $date;
    }
}
