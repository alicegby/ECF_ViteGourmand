<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Plats;

#[ORM\Entity]
class CategoryFood {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"category", targetEntity:Plats::class)]
    private Collection $plats;

    public function __construct() { 
        $this->plats = new ArrayCollection(); 
    }

    // Getters

    public function getId(): ?int { 
        return $this->id; 
    }

    public function getLibelle(): ?string { 
        return $this->libelle; 
    }

    public function getPlats(): Collection { 
        return $this->plats; 
    }

    // Setters

    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; return $this; 
    }

    //  Methods to manage Plats

    public function addPlat(Plats $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->setCategory($this);
        }
        return $this;
    }

    public function removePlat(Plats $plat): self
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getCategory() === $this) {
                $plat->setCategory(null);
            }
        }
        return $this;
    }
}