<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Entity\UtilisateurBadge;
use App\Entity\Commande;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100, unique:true)]
    private ?string $email = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $motDePasse = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $nom = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $prenom = null;

    #[ORM\Column(type:"string", length:20)]
    private ?string $telephone = null;

    #[ORM\Column(type:"string", length:255)]
    private ?string $adressePostale = null;

    #[ORM\Column(type:"string", length:10)]
    private ?string $codePostal = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $ville = null;

    #[ORM\Column(type:"datetime", length:100)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToMany(mappedBy:"utilisateur", targetEntity:UtilisateurBadge::class)]
    private Collection $utilisateurBadge;

    #[ORM\OneToMany(mappedBy:"utilisateur", targetEntity:Commande::class)]
    private Collection $commandes;

    #[ORM\Column(type:"json")]
    private array $roles = [];

    public function __construct()
    {
        $this->utilisateurBadge = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function getMotDePasse(): ?string { return $this->motDePasse; }
    public function getNom(): ?string { return $this->nom; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function getAdressePostale(): ?string { return $this->adressePostale; }
    public function getCodePostal(): ?string { return $this->codePostal; }
    public function getVille(): ?string { return $this->ville; }
    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function getRoles(): array { return array_unique(array_merge($this->roles, ['ROLE_USER'])); }

    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setMotDePasse(string $motDePasse): self { $this->motDePasse = $motDePasse; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }
    public function setAdressePostale(?string $adressePostale): self { $this->adressePostale = $adressePostale; return $this; }
    public function setCodePostal(?string $codePostal): self { $this->codePostal = $codePostal; return $this; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getUserIdentifier(): string { return (string)$this->email; }
    public function getPassword(): string { return (string)$this->motDePasse; }
    public function eraseCredentials(): void {}

    // Gestion des badges
    public function getUtilisateurBadge(): Collection
    {
        return $this->utilisateurBadge;
    }

    public function addUtilisateurBadge(UtilisateurBadge $ub): self {
        if (!$this->utilisateurBadge->contains($ub)) {
            $this->utilisateurBadge->add($ub);
            $ub->setUtilisateur($this);
        }
        return $this;
    }

    public function removeUtilisateurBadge(UtilisateurBadge $ub): self {
        if ($this->utilisateurBadge->removeElement($ub)) {
            if ($ub->getUtilisateur() === $this) {
                $ub->setUtilisateur(null);
            }
        }
        return $this;
    }

    public function hasBadge(?Badge $badge): bool
    {
        if (!$badge) return false;
        foreach ($this->utilisateurBadge as $ub) {
            if ($ub->getBadge()?->getId() === $badge->getId()) {
                return true;
            }
        }
        return false;
    }

    public function getCommandes(): Collection
{
    return $this->commandes;
}

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }
        return $this;
    }
}