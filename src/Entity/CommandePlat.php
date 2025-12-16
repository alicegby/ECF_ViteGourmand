<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Plats;
use App\Entity\CategoryFood;

#[ORM\Entity]
class CommandePlat
{
    #[ORM\Id, ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandePlats")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Id, ORM\ManyToOne(targetEntity: Plats::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plats $plat = null;

    #[ORM\ManyToOne(targetEntity: CategoryFood::class)]
    private ?CategoryFood $categoryPlat = null;

    // Getters & Setters
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): self {
        $this->commande = $commande;
        return $this;
    }

    public function getPlat(): ?Plats { return $this->plat; }
    public function setPlat(?Plats $plat): self {
        $this->plat = $plat;
        return $this;
    }

    public function getCategoryPlat(): ?CategoryFood { return $this->categoryPlat; }
    public function setCategoryPlat(?CategoryFood $categoryPlat): self {
        $this->categoryPlat = $categoryPlat;
        return $this;
    }
}