<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Menu;

#[ORM\Entity]
#[ORM\Table(name: 'conditions')]
class Condition
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: "conditions")]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getLibelle(): ?string { return $this->libelle; }
    public function getMenus(): Collection { return $this->menus; }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function addMenu(Menu $menu, bool $updateMenu = true): self {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            if ($updateMenu) {
                $menu->addCondition($this, false);
            }
        }
        return $this;
    }

    public function removeMenu(Menu $menu, bool $updateMenu = true): self {
        if ($this->menus->removeElement($menu)) {
            if ($updateMenu) {
                $menu->removeCondition($this, false);
            }
        }
        return $this;
    }
}