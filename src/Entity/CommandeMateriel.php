<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Materiel;

#[ORM\Entity]
class CommandeMateriel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandeMateriels")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Materiel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Materiel $materiel = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantite = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type:"boolean")]
    private bool $rendu = false;

    #[ORM\Column(type:"date", nullable:true)]
    private ?\DateTimeInterface $dateEnvoiMail = null;

    #[ORM\Column(type:"date", nullable:true)]
    private ?\DateTimeInterface $dateLimite = null;

    #[ORM\Column(type:"boolean")]
    private bool $penaliteAppliquee = false;

    #[ORM\Column(type:"decimal", precision:10, scale:2, nullable:true)]
    private ?string $montantPenalite = null;

    // Getters 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function getMateriel(): ?Materiel
    {
        return $this->materiel;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function isRendu(): bool
    {
        return $this->rendu;
    }

    public function getDateEnvoiMail(): ?\DateTimeInterface
    {
        return $this->dateEnvoiMail;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->dateLimite;
    }

    public function isPenaliteAppliquee(): bool
    {
        return $this->penaliteAppliquee;
    }

    public function getMontantPenalite(): ?string
    {
        return $this->montantPenalite;
    }

    // Setters
    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function setMateriel(?Materiel $materiel): self
    {
        $this->materiel = $materiel;

        // On récupère le prix du matériel au moment de la commande
        if ($materiel !== null) {
            $this->prixUnitaire = $materiel->getPrixPiece();
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

    public function setRendu(bool $rendu): self
    {
        $this->rendu = $rendu;
        return $this;
    }

    public function setDateEnvoiMail(?\DateTimeInterface $dateEnvoiMail): self
    {
        $this->dateEnvoiMail = $dateEnvoiMail;
        return $this;
    }

    public function setDateLimite(?\DateTimeInterface $dateLimite): self
    {
        $this->dateLimite = $dateLimite;
        return $this;
    }

    public function setPenaliteAppliquee(bool $penaliteAppliquee): self
    {
        $this->penaliteAppliquee = $penaliteAppliquee;
        return $this;
    }

    public function setMontantPenalite(?string $montantPenalite): self
    {
        $this->montantPenalite = $montantPenalite;
        return $this;
    }
}