<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Materiel;

#[ORM\Entity]
class CategoryMateriel {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"category", targetEntity:Materiel::class)]
    private Collection $materiel;

    public function __construct() { 
        $this->materiel = new ArrayCollection(); 
    }

    // Getters

    public function getId(): ?int { 
        return $this->id; 
    }

    public function getLibelle(): ?string { 
        return $this->libelle; 
    }

    public function getMateriel(): Collection { 
        return $this->materiel; 
    }

    // Setters

    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; return $this; 
    }

    // Methods to manage Materiel

    public function addMateriel(Materiel $materiel): self
    {
        if (!$this->materiel->contains($materiel)) {
            $this->materiel->add($materiel);
            $materiel->setCategory($this);
        }
        return $this;
    }

    public function removeMateriel(Materiel $materiel): self
    {
        if ($this->materiel->removeElement($materiel)) {
            // set the owning side to null (unless already changed)
            if ($materiel->getCategory() === $this) {
                $materiel->setCategory(null);
            }
        }
        return $this;
    }
}