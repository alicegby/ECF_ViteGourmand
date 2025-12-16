<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Menu;
use App\Entity\Plats;
use App\Entity\Boissons;
use App\Entity\Fromages;
use App\Entity\Materiel;
use App\Entity\Personnel;
use App\Entity\Commande;
use App\Entity\Employe;

#[ORM\Entity]
class MouvementStock {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $typeMouvement = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(targetEntity: Boissons::class)]
    private ?Boissons $boisson = null;

    #[ORM\ManyToOne(targetEntity: Fromages::class)]
    private ?Fromages $fromage = null;

    #[ORM\ManyToOne(targetEntity: Materiel::class)]
    private ?Materiel $materiel = null;

    #[ORM\ManyToOne(targetEntity: Personnel::class)]
    private ?Personnel $personnel = null;

    #[ORM\ManyToOne(targetEntity: Plats::class)]
    private ?Plats $plat = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantiteAvant = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantiteMouvement = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantiteApres = null;

    #[ORM\Column(type:"string", length:250, nullable:true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(targetEntity: Commande::class)]
    private ?Commande $commande = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateMouvement = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    private ?Employe $employe = null;

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTypeMouvement(): ?string { return $this->typeMouvement; }
    public function getMenu(): ?Menu { return $this->menu; }
    public function getBoisson(): ?Boissons { return $this->boisson; }
    public function getFromage(): ?Fromages { return $this->fromage; }
    public function getMateriel(): ?Materiel { return $this->materiel; }
    public function getPersonnel(): ?Personnel { return $this->personnel; }
    public function getPlat(): ?Plats { return $this->plat; }
    public function getQuantiteAvant(): ?int { return $this->quantiteAvant; }
    public function getQuantiteMouvement(): ?int { return $this->quantiteMouvement; }
    public function getQuantiteApres(): ?int { return $this->quantiteApres; }
    public function getMotif(): ?string { return $this->motif; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function getDateMouvement(): ?\DateTimeInterface { return $this->dateMouvement; }
    public function getEmploye(): ?Employe { return $this->employe; }

    // Setters
    public function setTypeMouvement(string $typeMouvement): self { $this->typeMouvement = $typeMouvement; return $this; }
    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }
    public function setBoisson(?Boissons $boisson): self { $this->boisson = $boisson; return $this; }
    public function setFromage(?Fromages $fromage): self { $this->fromage = $fromage; return $this; }
    public function setMateriel(?Materiel $materiel): self { $this->materiel = $materiel; return $this; }
    public function setPersonnel(?Personnel $personnel): self { $this->personnel = $personnel; return $this; }
    public function setPlat(?Plats $plat): self { $this->plat = $plat; return $this; }
    public function setQuantiteAvant(int $quantiteAvant): self { $this->quantiteAvant = $quantiteAvant; return $this; }
    public function setQuantiteMouvement(int $quantiteMouvement): self { $this->quantiteMouvement = $quantiteMouvement; return $this; }
    public function setQuantiteApres(int $quantiteApres): self { $this->quantiteApres = $quantiteApres; return $this; }
    public function setMotif(?string $motif): self { $this->motif = $motif; return $this; }
    public function setCommande(?Commande $commande): self { $this->commande = $commande; return $this; }
    public function setDateMouvement(\DateTimeInterface $dateMouvement): self { $this->dateMouvement = $dateMouvement; return $this; }
    public function setEmploye(?Employe $employe): self { $this->employe = $employe; return $this; }
}