<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Boissons;

#[ORM\Entity]
class CategoryDrink {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"category", targetEntity:Boissons::class)]
    private Collection $boissons;

    public function __construct() { 
        $this->boissons = new ArrayCollection(); 
    }

    // Getters

    public function getId(): ?int { 
        return $this->id; 
    }

    public function getLibelle(): ?string { 
        return $this->libelle; 
    }

    public function getBoissons(): Collection { 
        return $this->boissons; 
    }

    // Setters

    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; return $this; 
    }

    // Methods to manage Boissons

    public function addBoisson(Boissons $boisson): self
    {
        if (!$this->boissons->contains($boisson)) {
            $this->boissons->add($boisson);
            $boisson->setCategory($this);
        }
        return $this;
    }

    public function removeBoisson(Boissons $boisson): self
    {
        if ($this->boissons->removeElement($boisson)) {
            // set the owning side to null (unless already changed)
            if ($boisson->getCategory() === $this) {
                $boisson->setCategory(null);
            }
        }
        return $this;
    }
}