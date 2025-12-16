<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class StatutAvis
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50, unique:true)]
    private ?string $libelle = null;

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    // Setter 
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }
} 