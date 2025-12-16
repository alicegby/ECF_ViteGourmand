<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Newsletter
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:250, unique:true)]
    private ?string $email = null;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    private ?string $prenom = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(type:"boolean")]
    private bool $actif = true;

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    // Setters 
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }
}