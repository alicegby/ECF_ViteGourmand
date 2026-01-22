<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Personnel;

#[ORM\Entity]
class CommandePersonnel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandePersonnels")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Personnel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personnel = null;

    #[ORM\Column(type:"integer")]
    private ?int $heures = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantite = 1;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixUnitaire = null;

    // Getters 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function getHeures(): ?int
    {
        return $this->heures;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    // Setters
    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        // On récupère le prix à l'heure depuis l'entité Personnel
        if ($personnel !== null) {
            $this->prixUnitaire = $personnel->getPrixHeure();
        }

        return $this;
    }

    public function setHeures(?int $heures): self
    {
        $this->heures = $heures;
        return $this;
    }

    public function setQuantite (?int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function setPrixUnitaire(?string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }
}