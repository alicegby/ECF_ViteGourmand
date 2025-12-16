<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reduction
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $type = null;

    #[ORM\Column(type:"integer", nullable: true)]
    private ?int $conditionQuantite = null;

    #[ORM\Column(type:"decimal", precision:5, scale:2)]
    private ?string $reduction = null;

    #[ORM\Column(type:"boolean")]
    private bool $actif = true;

    // Getters 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getConditionQuantite(): ?int
    {
        return $this->conditionQuantite;
    }

    public function getReduction(): ?string
    {
        return $this->reduction;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    // Setters
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setConditionQuantite(?int $conditionQuantite): self
    {
        $this->conditionQuantite = $conditionQuantite;
        return $this;
    }

    public function setReduction(string $reduction): self
    {
        $this->reduction = $reduction;
        return $this;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }
}