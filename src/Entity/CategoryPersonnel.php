<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Personnel;

#[ORM\Entity]
class CategoryPersonnel {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"category", targetEntity:Personnel::class)]
    private Collection $personnel;

    public function __construct() { 
        $this->personnel = new ArrayCollection(); 
    }

    // Getters

    public function getId(): ?int { 
        return $this->id; 
    }

    public function getLibelle(): ?string { 
        return $this->libelle; 
    }

    public function getPersonnel(): Collection { 
        return $this->personnel; 
    }

    // Setters

    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; return $this; 
    }

    // Methods to manage Personnel

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnel->contains($personnel)) {
            $this->personnel->add($personnel);
            $personnel->setCategory($this);
        }
        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnel->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getCategory() === $this) {
                $personnel->setCategory(null);
            }
        }
        return $this;
    }
}