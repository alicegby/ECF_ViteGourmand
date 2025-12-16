<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Fromages;

#[ORM\Entity]
class CommandeFromage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandeFromages")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Fromages::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fromages $fromage = null;

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

    public function getFromage(): ?Fromages
    {
        return $this->fromage;
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

    public function setFromage(?Fromages $fromage): self
    {
        $this->fromage = $fromage;

        // On récupère le prix du fromage au moment de la commande
        if ($fromage !== null) {
            $this->prixUnitaire = $fromage->getPrixParFromage();
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