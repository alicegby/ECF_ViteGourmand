<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Menu;

#[ORM\Entity]
class ImageMenu
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: "images")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $url = null;

    #[ORM\Column(type:"string", length:100, nullable:true)]
    private ?string $altText = null;

    #[ORM\Column(type:"integer")]
    private int $ordre = 0;

    #[ORM\Column(type:"boolean")]
    private bool $estPrincipale = false;

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getMenu(): ?Menu { return $this->menu; }
    public function getUrl(): ?string { return $this->url; }
    public function getAltText(): ?string { return $this->altText; }
    public function getOrdre(): int { return $this->ordre; }
    public function isPrincipale(): bool { return $this->estPrincipale; }

    // Setters
    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }
    public function setUrl(string $url): self { $this->url = $url; return $this; }
    public function setAltText(?string $altText): self { $this->altText = $altText; return $this; }
    public function setOrdre(int $ordre): self { $this->ordre = $ordre; return $this; }
    public function setEstPrincipale(bool $estPrincipale): self { $this->estPrincipale = $estPrincipale; return $this; }
}