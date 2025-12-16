<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use App\Entity\Badge;

#[ORM\Entity]
#[ORM\Table(
    name:"utilisateur_badge",
    uniqueConstraints:[new ORM\UniqueConstraint(name:"uniq_utilisateur_badge", columns:["utilisateur_id","badge_id"])]
)]
class UtilisateurBadge
{
    #[ORM\Id, ORM\ManyToOne(targetEntity:Utilisateur::class, inversedBy:"utilisateurBadge")]
    #[ORM\JoinColumn(nullable:false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Id, ORM\ManyToOne(targetEntity:Badge::class, inversedBy:"utilisateurBadges")]
    #[ORM\JoinColumn(nullable:false)]
    private ?Badge $badge = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateObtention = null;

    // Getters
    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function getBadge(): ?Badge { return $this->badge; }
    public function getDateObtention(): ?\DateTimeInterface { return $this->dateObtention; }

    // Setters
    public function setUtilisateur(?Utilisateur $u): self { $this->utilisateur = $u; return $this; }
    public function setBadge(?Badge $b): self { $this->badge = $b; return $this; }
    public function setDateObtention(?\DateTimeInterface $d): self { $this->dateObtention = $d; return $this; }
}