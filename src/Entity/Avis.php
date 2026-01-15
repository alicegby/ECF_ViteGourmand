<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Commande;
use App\Entity\StatutAvis;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name: "avis")]
class Avis
{
    #[ORM\Id, ORM\GeneratedValue]
    #[ORM\Column(name: "avis_id", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "avis")]
    #[ORM\JoinColumn(name: "commande_id", referencedColumnName: "id", nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(type: "smallint")]
    private ?int $notes = null;

    #[ORM\Column(name: "avis", type: "string", length: 250)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(targetEntity: StatutAvis::class)]
    #[ORM\JoinColumn(name: "statut_avis_id", referencedColumnName: "id", nullable: false)]
    private ?StatutAvis $statut = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(name: "valide_par", referencedColumnName: "id", nullable: true)]
    private ?Employe $validePar = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    // --- Getters ---
    public function getId(): ?int { return $this->id; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function getNotes(): ?int { return $this->notes; }
    public function getContenu(): ?string { return $this->contenu; }
    public function getStatut(): ?StatutAvis { return $this->statut; }
    public function getValidePar(): ?Employe { return $this->validePar; }
    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function getDateValidation(): ?\DateTimeInterface { return $this->dateValidation; }

    // --- Setters ---
    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function setNotes(int $notes): self { $this->notes = $notes; return $this; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function setStatut(StatutAvis $statut): self { $this->statut = $statut; return $this; }
    public function setValidePar(?Employe $validePar): self { $this->validePar = $validePar; return $this; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
    public function setDateValidation(?\DateTimeInterface $dateValidation): self { $this->dateValidation = $dateValidation; return $this; }

    // --- Méthode métier ---
    public function isAccepted(): bool
    {
        return $this->statut?->getLibelle() === 'Accepté';
    }
}