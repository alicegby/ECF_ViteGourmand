<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Reduction;

#[ORM\Entity]
class CommandeReduction
{
    #[ORM\Id, ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandeReductions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Id, ORM\ManyToOne(targetEntity: Reduction::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reduction $reduction = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $montantReduction = null;

    // Getters
    public function getCommande(): ?Commande { return $this->commande; }
    public function getReduction(): ?Reduction { return $this->reduction; }
    public function getMontantReduction(): ?string { return $this->montantReduction; }

    // Setters
    public function setCommande(?Commande $commande): self { 
        $this->commande = $commande; 
        return $this; 
    }

    public function setReduction(?Reduction $reduction): self { 
        $this->reduction = $reduction; 
        return $this; 
    }

    public function setMontantReduction(?string $montantReduction): self {
        $this->montantReduction = $montantReduction; 
        return $this; 
    }
}