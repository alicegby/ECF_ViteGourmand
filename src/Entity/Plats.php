<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CategoryFood;
use App\Entity\Employe;
use App\Entity\Allergenes;
use App\Entity\MenuPlat;

#[ORM\Entity]
class Plats
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $titrePlat = null;

    #[ORM\ManyToOne(targetEntity:CategoryFood::class, inversedBy:"plats")]
    private ?CategoryFood $category = null;

    #[ORM\Column(type:"string", length:300)]
    private ?string $description = null;

    #[ORM\Column(type:"string", length:250, nullable:true)]
    private ?string $image = null;

    #[ORM\Column(type:"string", length:250, nullable:true)]
    private ?string $altTexte = null;

    #[ORM\ManyToOne(targetEntity:Employe::class)]
    #[ORM\JoinColumn(nullable:true)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\ManyToMany(targetEntity:Allergenes::class, inversedBy:"plats")]
    #[ORM\JoinTable(name:"plat_allergene")]
    private Collection $allergenes;

    #[ORM\OneToMany(mappedBy:"plat", targetEntity:MenuPlat::class)]
    private Collection $menuPlats;

    public function __construct()
    {
        $this->allergenes = new ArrayCollection();
        $this->menuPlats = new ArrayCollection();
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function getTitrePlat(): ?string { return $this->titrePlat; }
    public function getCategory(): ?CategoryFood { return $this->category; }
    public function getDescription(): ?string { return $this->description; }
    public function getImage(): ?string { return $this->image; }
    public function getAltTexte(): ?string { return $this->altTexte; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }
    public function getAllergenes(): Collection { return $this->allergenes; }
    public function getMenuPlats(): Collection { return $this->menuPlats; }

    public function setTitrePlat(string $titrePlat): self { $this->titrePlat = $titrePlat; return $this; }
    public function setCategory(?CategoryFood $category): self { $this->category = $category; return $this; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
    public function setAltTexte(?string $altTexte): self { $this->altTexte = $altTexte; return $this; }
    public function setModifiePar(?Employe $employe): self { $this->modifiePar = $employe; return $this; }
    public function setDateModif(?\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }

    // Allergene management
    public function addAllergene(Allergenes $allergene): self {
        if (!$this->allergenes->contains($allergene)) {
            $this->allergenes->add($allergene);
        }
        return $this;
    }

    public function removeAllergene(Allergenes $allergene): self {
        $this->allergenes->removeElement($allergene);
        return $this;
    }

    // MenuPlat management
    public function addMenuPlat(MenuPlat $mp): self {
        if (!$this->menuPlats->contains($mp)) {
            $this->menuPlats->add($mp);
            $mp->setPlat($this);
        }
        return $this;
    }

    public function removeMenuPlat(MenuPlat $mp): self {
        if ($this->menuPlats->removeElement($mp)) {
            if ($mp->getPlat() === $this) {
                $mp->setPlat(null);
            }
        }
        return $this;
    }
}