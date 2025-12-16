<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Menu;

#[ORM\Entity]
class Theme
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"theme", targetEntity:Menu::class)]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getLibelle(): ?string { return $this->libelle; }
    public function getMenus(): Collection { return $this->menus; }

    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }

    public function addMenu(Menu $menu): self {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->setTheme($this);
        }
        return $this;
    }

    public function removeMenu(Menu $menu): self {
        if ($this->menus->removeElement($menu)) {
            if ($menu->getTheme() === $this) {
                $menu->setTheme(null);
            }
        }
        return $this;
    }
}