<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name:"horaire")]
class Horaire {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $jour = null;

    #[ORM\Column(type:"time")]
    private ?\DateTimeInterface $heureOuverture = null;

    #[ORM\Column(type:"time")]
    private ?\DateTimeInterface $heureFermeture = null;

    #[ORM\ManyToOne(targetEntity:Employe::class)]
    #[ORM\JoinColumn(name:"modifie_par", referencedColumnName:"id")]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateModif = null;

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getJour(): ?string { return $this->jour; }
    public function getHeureOuverture(): ?\DateTimeInterface { return $this->heureOuverture; }
    public function getHeureFermeture(): ?\DateTimeInterface { return $this->heureFermeture; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }

    // Setters
    public function setJour(string $jour): self { $this->jour = $jour; return $this; }
    public function setHeureOuverture(\DateTimeInterface $heureOuverture): self { $this->heureOuverture = $heureOuverture; return $this; }
    public function setHeureFermeture(\DateTimeInterface $heureFermeture): self { $this->heureFermeture = $heureFermeture; return $this; }
    public function setModifiePar(?Employe $modifiePar): self { $this->modifiePar = $modifiePar; return $this; }
    public function setDateModif(\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }

}