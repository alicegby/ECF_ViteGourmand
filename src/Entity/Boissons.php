<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Employe;
use App\Entity\CategoryDrink;

#[ORM\Entity]
#[ORM\Table(name: "boissons")]
class Boissons
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $titreBoisson = null;

    #[ORM\ManyToOne(targetEntity: CategoryDrink::class, inversedBy:"boissons")]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategoryDrink $category = null;

    #[ORM\Column(type:"string", length:150)]
    private ?string $description = null;

    #[ORM\Column(type:"integer")]
    private int $stock = 0;

    #[ORM\Column(type:"integer")]
    private int $qteParPers = 1;

    #[ORM\Column(type:"integer")]
    private int $minCommande = 1;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixParBouteille = null;

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

    public function getTitreBoisson(): ?string
    {
        return $this->titreBoisson;
    }

    public function getCategory(): ?CategoryDrink
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

    public function getQteParPers(): int
    {
        return $this->qteParPers;
    }

    public function getMinCommande(): int
    {
        return $this->minCommande;
    }

    public function getPrixParBouteille(): ?float
    {
        return $this->prixParBouteille !== null ? (float) $this->prixParBouteille : null;
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

    // Setters 

    public function setTitreBoisson(string $titreBoisson): self
    {
        $this->titreBoisson = $titreBoisson;
        return $this;
    }

    public function setCategory(?CategoryDrink $category): self
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

    public function setQteParPers(int $qteParPers): self
    {
        $this->qteParPers = $qteParPers;
        return $this;
    }

    public function setMinCommande(int $minCommande): self
    {
        $this->minCommande = $minCommande;
        return $this;
    }

    public function setPrixParBouteille(float|string $prixParBouteille): self
    {
        $this->prixParBouteille = (string) $prixParBouteille;
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