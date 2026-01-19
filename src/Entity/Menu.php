<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Regime;
use App\Entity\Theme;
use App\Entity\Condition;
use App\Entity\Employe;
use App\Entity\MenuPlat;
use App\Entity\ImageMenu;
use App\Repository\MenuRepository;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity:Regime::class, inversedBy:"menus")]
    private ?Regime $regime = null;

    #[ORM\ManyToOne(targetEntity:Theme::class, inversedBy:"menus")]
    private ?Theme $theme = null;

    #[ORM\Column(type:"integer")]
        private ?int $stock = null;

    #[ORM\Column(type:"integer")]
private ?int $nbPersMin = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixParPersonne = null;

    #[ORM\Column(type:"string", length:300)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity:Condition::class, inversedBy:"menus", fetch:"EAGER")]
    #[ORM\JoinTable(name:"menu_condition")]
    private Collection $conditions;

    #[ORM\ManyToOne(targetEntity:Employe::class)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\OneToMany(mappedBy:"menu", targetEntity:MenuPlat::class, cascade:["persist", "remove"])]
    private Collection $menuPlats;

    #[ORM\OneToMany(targetEntity: ImageMenu::class, mappedBy: "menu", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->menuPlats = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->conditions = new ArrayCollection();
    }

    // ----- Getters -----
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getRegime(): ?Regime { return $this->regime; }
    public function getTheme(): ?Theme { return $this->theme; }
    public function getStock(): ?int { return $this->stock; }
    public function getNbPersMin(): ?int { return $this->nbPersMin; }
    public function getPrixParPersonne(): ?string { return $this->prixParPersonne; }
    public function getDescription(): ?string { return $this->description; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }
    public function getMenuPlats(): Collection { return $this->menuPlats; }
    public function getImages(): Collection { return $this->images; }
    public function getConditions(): Collection { return $this->conditions; }

    // ----- Setters -----
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setRegime(?Regime $regime): self { $this->regime = $regime; return $this; }
    public function setTheme(?Theme $theme): self { $this->theme = $theme; return $this; }
    public function setStock(int $stock): self { $this->stock = $stock; return $this; }
    public function setNbPersMin(int $nbPersMin): self { $this->nbPersMin = $nbPersMin; return $this; }
    public function setPrixParPersonne(string $prix): self { $this->prixParPersonne = $prix; return $this; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function setModifiePar(?Employe $employe): self { $this->modifiePar = $employe; return $this; }
    public function setDateModif(?\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }

    // ----- Relation management MenuPlat -----
    public function addMenuPlat(MenuPlat $mp): self {
        if (!$this->menuPlats->contains($mp)) {
            $this->menuPlats->add($mp);
            $mp->setMenu($this);
        }
        return $this;
    }

    public function removeMenuPlat(MenuPlat $mp): self {
        if ($this->menuPlats->removeElement($mp)) {
            if ($mp->getMenu() === $this) {
                $mp->setMenu(null);
            }
        }
        return $this;
    }

    // ----- Relation management ImageMenu -----
    public function addImage(ImageMenu $img): self {
        if (!$this->images->contains($img)) {
            $this->images->add($img);
            $img->setMenu($this);
        }
        return $this;
    }

    public function removeImage(ImageMenu $img): self {
        if ($this->images->removeElement($img)) {
            if ($img->getMenu() === $this) {
                $img->setMenu(null);
            }
        }
        return $this;
    }

    // ----- Relation management Conditions -----
public function addCondition(Condition $condition, bool $updateCondition = true): self {
    if (!$this->conditions->contains($condition)) { // <-- correction ici
        $this->conditions->add($condition);
        if ($updateCondition) {
            $condition->addMenu($this, false);
        }
    }
    return $this;
}

public function removeCondition(Condition $condition, bool $updateCondition = true): self {
    if ($this->conditions->removeElement($condition)) {
        if ($updateCondition) {
            $condition->removeMenu($this, false);
        }
    }
    return $this;
}

}