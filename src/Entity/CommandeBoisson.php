<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Boissons;

#[ORM\Entity]
class CommandeBoisson
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandeBoissons")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Boissons::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Boissons $boisson = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantite = null;

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

    public function getBoisson(): ?Boissons
    {
        return $this->boisson;
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

    public function setBoisson(?Boissons $boisson): self
    {
        $this->boisson = $boisson;

        // On récupère le prix actuel de la boisson au moment de la commande
        if ($boisson !== null) {
            $this->prixUnitaire = $boisson->getPrixParBouteille();
        }

        return $this;
    }

    public function setQuantite(?int $quantite): self
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