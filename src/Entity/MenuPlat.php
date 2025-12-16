<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Menu;
use App\Entity\Plats;

#[ORM\Entity]
class MenuPlat
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:Menu::class, inversedBy:"menuPlats")]
    #[ORM\JoinColumn(nullable:false)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(targetEntity:Plats::class, inversedBy:"menuPlats")]
    #[ORM\JoinColumn(nullable:false)]
    private ?Plats $plat = null;

    #[ORM\Column(type:"smallint")]
    private ?int $ordre = null;

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function getMenu(): ?Menu { return $this->menu; }
    public function getPlat(): ?Plats { return $this->plat; }
    public function getOrdre(): ?int { return $this->ordre; }

    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }
    public function setPlat(?Plats $plat): self { $this->plat = $plat; return $this; }
    public function setOrdre(?int $ordre): self { $this->ordre = $ordre; return $this; }
}