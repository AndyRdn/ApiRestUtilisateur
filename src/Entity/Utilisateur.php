<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Couchbase\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements PasswordAuthenticatedUserInterface
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
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    private ?string $mdpSimple = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?LoginTentative $tentative = null;

    /**
     * @var Collection<int, DoubleAuthentification>
     */
    #[ORM\OneToMany(targetEntity: DoubleAuthentification::class, mappedBy: 'utilisateur')]
    private Collection $doubleAuth;

    public function __construct()
    {
        $this->doubleAuth = new ArrayCollection();
    }

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

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

    public function getMdpSimple(): ?string
    {
        return $this->mdpSimple;
    }

    public function setMdpSimple(?string $mdpSimple): static
    {
        $this->mdpSimple = $mdpSimple;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function getTentative(): ?LoginTentative
    {
        return $this->tentative;
    }

    public function setTentative(LoginTentative $tentative): static
    {
        // set the owning side of the relation if necessary
        if ($tentative->getUtilisateur() !== $this) {
            $tentative->setUtilisateur($this);
        }

        $this->tentative = $tentative;

        return $this;
    }

    /**
     * @return Collection<int, DoubleAuthentification>
     */
    public function getDoubleAuth(): Collection
    {
        return $this->doubleAuth;
    }

    public function addDoubleAuth(DoubleAuthentification $doubleAuth): static
    {
        if (!$this->doubleAuth->contains($doubleAuth)) {
            $this->doubleAuth->add($doubleAuth);
            $doubleAuth->setUtilisateur($this);
        }

        return $this;
    }

    public function removeDoubleAuth(DoubleAuthentification $doubleAuth): static
    {
        if ($this->doubleAuth->removeElement($doubleAuth)) {
            // set the owning side to null (unless already changed)
            if ($doubleAuth->getUtilisateur() === $this) {
                $doubleAuth->setUtilisateur(null);
            }
        }

        return $this;
    }
}
