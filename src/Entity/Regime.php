<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Menu;

#[ORM\Entity]
class Regime
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"regime", targetEntity:Menu::class)]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getLibelle(): ?string { return $this->libelle; }
    public function getMenus(): Collection { return $this->menus; }

    // Setters
    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }

    // Relation management
    public function addMenu(Menu $menu): self {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->setRegime($this);
        }
        return $this;
    }

    public function removeMenu(Menu $menu): self {
        if ($this->menus->removeElement($menu)) {
            if ($menu->getRegime() === $this) {
                $menu->setRegime(null);
            }
        }
        return $this;
    }
}