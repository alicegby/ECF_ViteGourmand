<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\CategoryPersonnel;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name: "personnel")]
class Personnel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $titrePersonnel = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $description= null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixHeure = null;

    #[ORM\Column(type:"integer")]
    private int $stock = 0;

    #[ORM\ManyToOne(targetEntity: CategoryPersonnel::class, inversedBy:"personnel")]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategoryPersonnel $category = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $dateModif = null;

    // Getters 
    public function getId(): ?int { return $this->id; }
    public function getTitrePersonnel(): ?string { return $this->titrePersonnel; }
    public function getDescription(): ?string { return $this->description; }
    public function getPrixHeure(): ?string { return $this->prixHeure !== null ? (float) $this->prixHeure : null; }
    public function getStock(): int { return $this->stock; }
    public function getCategory(): ?CategoryPersonnel { return $this->category; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }

    // Setters
    public function setTitrePersonnel(string $titrePersonnel): self { $this->titrePersonnel = $titrePersonnel; return $this; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function setPrixHeure(string $prixHeure): self { $this->prixHeure = $prixHeure; return $this; }
    public function setStock(int $stock): self { $this->stock = $stock; return $this; }
    public function setCategory(?CategoryPersonnel $category): self { $this->category = $category; return $this; }
    public function setModifiePar(?Employe $modifiePar): self { $this->modifiePar = $modifiePar; return $this; }
    public function setDateModif(?\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }
}