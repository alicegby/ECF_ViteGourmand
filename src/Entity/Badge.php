<?php

namespace App\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UtilisateurBadge;

#[ORM\Entity]
#[ORM\Table(name:"badge")]
class Badge
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $nom = null;

    #[ORM\Column(type:"string", length:500)]
    private ?string $description = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $icone = null;

    #[ORM\Column(type:"text")]
    private ?string $conditionObtention = null;

    #[ORM\Column(type:"boolean")]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy:"badge", targetEntity:UtilisateurBadge::class, cascade:["persist","remove"])]
    private Collection $utilisateurBadges;

    public function __construct()
    {
        $this->utilisateurBadges = new ArrayCollection();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getIcone(): ?string { return $this->icone; }
    public function getConditionObtention(): ?string { return $this->conditionObtention; }
    public function isActif(): bool { return $this->actif; }
    public function getUtilisateurBadges(): Collection { return $this->utilisateurBadges; }

    // Setters
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setDescription(string $desc): self { $this->description = $desc; return $this; }
    public function setIcone(string $icone): self { $this->icone = $icone; return $this; }
    public function setConditionObtention(string $cond): self { $this->conditionObtention = $cond; return $this; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    // Relation management
    public function addUtilisateurBadge(UtilisateurBadge $ub): self {
        if (!$this->utilisateurBadges->contains($ub)) {
            $this->utilisateurBadges->add($ub);
            $ub->setBadge($this);
        }
        return $this;
    }

    public function removeUtilisateurBadge(UtilisateurBadge $ub): self {
        if ($this->utilisateurBadges->removeElement($ub)) {
            if ($ub->getBadge() === $this) {
                $ub->setBadge(null);
            }
        }
        return $this;
    }
}