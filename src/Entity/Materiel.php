<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\CategoryMateriel;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name: "materiel")]
class Materiel 
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $titreMateriel = null;

    #[ORM\ManyToOne(targetEntity: CategoryMateriel::class, inversedBy: "materiel")]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategoryMateriel $category = null;

    #[ORM\Column(type:"string", length:200)]
    private ?string $description = null;

    #[ORM\Column(type:"integer")]
    private int $stock = 0;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixPiece = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $image = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $alt = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $caution = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime", nullable: true)]
    private ?\DateTimeInterface $dateModif = null;

    //  Getters 
    public function getId(): ?int { return $this->id; }
    public function getTitreMateriel(): ?string { return $this->titreMateriel; }
    public function getCategory(): ?CategoryMateriel { return $this->category; }
    public function getDescription(): ?string { return $this->description; }
    public function getStock(): int { return $this->stock; }
    public function getPrixPiece(): ?float { return $this->prixPiece !== null ? (float) $this->prixPiece : null; }
    public function getImage(): ?string { return $this->image; }
    public function getAlt(): ?string { return $this->alt; }
    public function getCaution(): ?float { return $this->caution !== null ? (float) $this->caution : null; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }

    // Setters 
    public function setTitreMateriel(string $titreMateriel): self { $this->titreMateriel = $titreMateriel; return $this; }
    public function setCategory(?CategoryMateriel $category): self { $this->category = $category; return $this; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function setStock(int $stock): self { $this->stock = $stock; return $this; }
    public function setPrixPiece(float|string $prixPiece): self { $this->prixPiece = (string) $prixPiece; return $this; }
    public function setImage(string $image): self { $this->image = $image; return $this; }
    public function setAlt(string $alt): self { $this->alt = $alt; return $this; }
    public function setCaution(float|string $caution): self { $this->caution = (string) $caution; return $this; }
    public function setModifiePar(?Employe $employe): self { $this->modifiePar = $employe; return $this; }
    public function setDateModif(?\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }
}