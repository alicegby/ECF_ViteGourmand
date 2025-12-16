<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Plats;

#[ORM\Entity]
class Allergenes
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $libelle = null;

    #[ORM\Column(type:"string", length:250, nullable:true)]
    private ?string $icone = null;

    #[ORM\ManyToMany(targetEntity: Plats::class, mappedBy:"allergenes")]
    private Collection $plats;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
    }

    // ----- Getters -----
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function getIcone(): ?string
    {
        return $this->icone; 
    }

    public function getPlats(): Collection
    {
        return $this->plats;
    }

    // ----- Setters -----
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;
        return $this;
    }

    // ----- Relation management -----
    public function addPlat(Plats $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
        }
        return $this;
    }

    public function removePlat(Plats $plat): self
    {
        $this->plats->removeElement($plat);
        return $this;
    }
}