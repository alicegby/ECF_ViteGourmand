<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Fromages;

#[ORM\Entity]
class CategoryCheese {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy:"category", targetEntity:Fromages::class)]
    private Collection $fromages;

    public function __construct() { 
        $this->fromages = new ArrayCollection(); 
    }

    // Getters

    public function getId(): ?int { 
        return $this->id; 
    }

    public function getLibelle(): ?string { 
        return $this->libelle; 
    }

    public function getFromages(): Collection { 
        return $this->fromages; 
    }

    // Setters

    public function setLibelle(string $libelle): self { 
        $this->libelle = $libelle; return $this; 
    }

    // Methods to manage Fromages

    public function addFromage(Fromages $fromage): self
    {
        if (!$this->fromages->contains($fromage)) {
            $this->fromages->add($fromage);
            $fromage->setCategory($this);
        }
        return $this;
    }

    public function removeFromage(Fromages $fromage): self
    {
        if ($this->fromages->removeElement($fromage)) {
            // set the owning side to null (unless already changed)
            if ($fromage->getCategory() === $this) {
                $fromage->setCategory(null);
            }
        }
        return $this;
    }
}