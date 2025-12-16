<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\CategoryCheese;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name: "fromages")]
class Fromages 
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $titreFromage = null;

    #[ORM\ManyToOne(targetEntity: CategoryCheese::class, inversedBy: "fromages")]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategoryCheese $category = null;

    #[ORM\Column(type:"string", length:150)]
    private ?string $description = null;

    #[ORM\Column(type:"integer")]
    private int $stock = 0;

    #[ORM\Column(type:"integer")]
    private int $minCommande = 1;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixParFromage = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $image = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $alt = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime", nullable: true)]
    private ?\DateTimeInterface $dateModif = null;

    //  Getters 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreFromage(): ?string
    {
        return $this->titreFromage;
    }

    public function getCategory(): ?CategoryCheese
    {
        return $this->category;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getMinCommande(): int
    {
        return $this->minCommande;
    }

    public function getPrixParFromage(): ?float
    {
        return $this->prixParFromage !== null ? (float) $this->prixParFromage : null;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function getModifiePar(): ?Employe
    {
        return $this->modifiePar;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    //  Setters

    public function setTitreFromage(string $titreFromage): self
    {
        $this->titreFromage = $titreFromage;
        return $this;
    }

    public function setCategory(?CategoryCheese $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function setMinCommande(int $minCommande): self
    {
        $this->minCommande = $minCommande;
        return $this;
    }

    public function setPrixParFromage(float|string $prixParFromage): self
    {
        $this->prixParFromage = (string) $prixParFromage;
        return $this;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;
        return $this;
    }

    public function setModifiePar(?Employe $employe): self
    {
        $this->modifiePar = $employe;
        return $this;
    }

    public function setDateModif(?\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;
        return $this;
    }
}