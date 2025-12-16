<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Utilisateur;
use App\Entity\Menu;
use App\Entity\StatutCommande;
use App\Entity\Employe;
use App\Entity\CommandePlat;
use App\Entity\CommandeBoisson;
use App\Entity\CommandeFromage;
use App\Entity\CommandePersonnel;
use App\Entity\CommandeMateriel;
use App\Entity\CommandeReduction;

#[ORM\Entity]
class Commande {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $numeroCommande = null;

    #[ORM\ManyToOne(targetEntity:Utilisateur::class)]
    private ?Utilisateur $client = null;

    #[ORM\ManyToOne(targetEntity:Menu::class)]
    private ?Menu $menu = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(type:"date")]
    private ?\DateTimeInterface $dateLivraison = null;

    #[ORM\Column(type:"time")]
    private ?\DateTimeInterface $heureLivraison = null;

    #[ORM\Column(type:"smallint")]
    private ?int $nbPersonne = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixMenu = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $montantOptions = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $montantReduction = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $prixTotal = null;

    #[ORM\ManyToOne(targetEntity:StatutCommande::class)]
    private ?StatutCommande $statutCommande = null;

    #[ORM\Column(type:"boolean")]
    private bool $pretMateriel = false;

    #[ORM\Column(type:"boolean")]
    private bool $restitutionMateriel = false;

    #[ORM\Column(type:"boolean")]
    private bool $pretPersonnel = false;

    #[ORM\ManyToOne(targetEntity:Employe::class)]
    private ?Employe $modifiePar = null;

    #[ORM\Column(type:"string", length:250, nullable:true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\Column(type:"string", length:250)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type:"string", length:10)]
    private ?string $codePostalLivraison = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $villeLivraison = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?string $fraisLivraison = null;

    #[ORM\Column(type:"decimal", precision:5, scale:2)]
    private ?string $distanceKm = null;

    // Collections
    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandePlat::class)]
    private Collection $commandePlats;

    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandeBoisson::class)]
    private Collection $commandeBoissons;

    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandeFromage::class)]
    private Collection $commandeFromages;

    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandePersonnel::class)]
    private Collection $commandePersonnels;

    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandeMateriel::class)]
    private Collection $commandeMateriels;

    #[ORM\OneToMany(mappedBy:"commande", targetEntity:CommandeReduction::class)]
    private Collection $commandeReductions;

    public function __construct()
    {
        $this->commandePlats = new ArrayCollection();
        $this->commandeBoissons = new ArrayCollection();
        $this->commandeFromages = new ArrayCollection();
        $this->commandePersonnels = new ArrayCollection();
        $this->commandeMateriels = new ArrayCollection();
        $this->commandeReductions = new ArrayCollection();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNumeroCommande(): ?string { return $this->numeroCommande; }
    public function getClient(): ?Utilisateur { return $this->client; }
    public function getMenu(): ?Menu { return $this->menu; }
    public function getDateCommande(): ?\DateTimeInterface { return $this->dateCommande; }
    public function getDateLivraison(): ?\DateTimeInterface { return $this->dateLivraison; }
    public function getHeureLivraison(): ?\DateTimeInterface { return $this->heureLivraison; }
    public function getNbPersonne(): ?int { return $this->nbPersonne; }
    public function getPrixMenu(): ?string { return $this->prixMenu; }
    public function getMontantOptions(): ?string { return $this->montantOptions; }
    public function getMontantReduction(): ?string { return $this->montantReduction; }
    public function getPrixTotal(): ?string { return $this->prixTotal; }
    public function getStatutCommande(): ?StatutCommande { return $this->statutCommande; }
    public function isPretMateriel(): bool { return $this->pretMateriel; }
    public function isRestitutionMateriel(): bool { return $this->restitutionMateriel; }
    public function isPretPersonnel(): bool { return $this->pretPersonnel; }
    public function getModifiePar(): ?Employe { return $this->modifiePar; }
    public function getMotifAnnulation(): ?string { return $this->motifAnnulation; }
    public function getDateModif(): ?\DateTimeInterface { return $this->dateModif; }
    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function getCodePostalLivraison(): ?string { return $this->codePostalLivraison; }
    public function getVilleLivraison(): ?string { return $this->villeLivraison; }
    public function getFraisLivraison(): ?string { return $this->fraisLivraison; }
    public function getDistanceKm(): ?string { return $this->distanceKm; }

    // Setters
    public function setNumeroCommande(string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }
    public function setClient(?Utilisateur $client): self { $this->client = $client; return $this; }
    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }
    public function setDateCommande(?\DateTimeInterface $dateCommande): self { $this->dateCommande = $dateCommande; return $this; }
    public function setDateLivraison(?\DateTimeInterface $dateLivraison): self { $this->dateLivraison = $dateLivraison; return $this; }
    public function setHeureLivraison(?\DateTimeInterface $heureLivraison): self { $this->heureLivraison = $heureLivraison; return $this; }
    public function setNbPersonne(?int $nbPersonne): self { $this->nbPersonne = $nbPersonne; return $this; }
    public function setPrixMenu(?string $prixMenu): self { $this->prixMenu = $prixMenu; return $this; }
    public function setMontantOptions(?string $montantOptions): self { $this->montantOptions = $montantOptions; return $this; }
    public function setMontantReduction(?string $montantReduction): self { $this->montantReduction = $montantReduction; return $this; }
    public function setPrixTotal(?string $prixTotal): self { $this->prixTotal = $prixTotal; return $this; }
    public function setStatutCommande(?StatutCommande $statutCommande): self { $this->statutCommande = $statutCommande; return $this; }
    public function setPretMateriel(bool $pretMateriel): self { $this->pretMateriel = $pretMateriel; return $this; }
    public function setRestitutionMateriel(bool $restitutionMateriel): self { $this->restitutionMateriel = $restitutionMateriel; return $this; }
    public function setPretPersonnel(bool $pretPersonnel): self { $this->pretPersonnel = $pretPersonnel; return $this; }
    public function setModifiePar(?Employe $modifiePar): self { $this->modifiePar = $modifiePar; return $this; }
    public function setMotifAnnulation(?string $motifAnnulation): self { $this->motifAnnulation = $motifAnnulation; return $this; }
    public function setDateModif(?\DateTimeInterface $dateModif): self { $this->dateModif = $dateModif; return $this; }
    public function setAdresseLivraison(?string $adresseLivraison): self { $this->adresseLivraison = $adresseLivraison; return $this; }
    public function setCodePostalLivraison(?string $codePostalLivraison): self { $this->codePostalLivraison = $codePostalLivraison; return $this; }
    public function setVilleLivraison(?string $villeLivraison): self { $this->villeLivraison = $villeLivraison; return $this; }
    public function setFraisLivraison(?string $fraisLivraison): self { $this->fraisLivraison = $fraisLivraison; return $this; }
    public function setDistanceKm(?string $distanceKm): self { $this->distanceKm = $distanceKm; return $this; }

    // Methods to manage collections
    public function getCommandePlats(): Collection { return $this->commandePlats; }
    public function addCommandePlat(CommandePlat $commandePlat): self { if (!$this->commandePlats->contains($commandePlat)) { $this->commandePlats->add($commandePlat); } return $this; }
    public function removeCommandePlat(CommandePlat $commandePlat): self { $this->commandePlats->removeElement($commandePlat); return $this; }

    public function getCommandeBoissons(): Collection { return $this->commandeBoissons; }
    public function addCommandeBoisson(CommandeBoisson $commandeBoisson): self { if (!$this->commandeBoissons->contains($commandeBoisson)) { $this->commandeBoissons->add($commandeBoisson); } return $this; }
    public function removeCommandeBoisson(CommandeBoisson $commandeBoisson): self { $this->commandeBoissons->removeElement($commandeBoisson); return $this; }

    public function getCommandeFromages(): Collection { return $this->commandeFromages; }
    public function addCommandeFromage(CommandeFromage $commandeFromage): self { if (!$this->commandeFromages->contains($commandeFromage)) { $this->commandeFromages->add($commandeFromage); } return $this; }
    public function removeCommandeFromage(CommandeFromage $commandeFromage): self { $this->commandeFromages->removeElement($commandeFromage); return $this; }

    public function getCommandePersonnels(): Collection { return $this->commandePersonnels; }
    public function addCommandePersonnel(CommandePersonnel $commandePersonnel): self { if (!$this->commandePersonnels->contains($commandePersonnel)) { $this->commandePersonnels->add($commandePersonnel); } return $this; }
    public function removeCommandePersonnel(CommandePersonnel $commandePersonnel): self { $this->commandePersonnels->removeElement($commandePersonnel); return $this; }

    public function getCommandeMateriels(): Collection { return $this->commandeMateriels; }
    public function addCommandeMateriel(CommandeMateriel $commandeMateriel): self { if (!$this->commandeMateriels->contains($commandeMateriel)) { $this->commandeMateriels->add($commandeMateriel); } return $this; }
    public function removeCommandeMateriel(CommandeMateriel $commandeMateriel): self { $this->commandeMateriels->removeElement($commandeMateriel); return $this; }

    public function getCommandeReductions(): Collection { return $this->commandeReductions; }
    public function addCommandeReduction(CommandeReduction $commandeReduction): self { if (!$this->commandeReductions->contains($commandeReduction)) { $this->commandeReductions->add($commandeReduction); } return $this; }
    public function removeCommandeReduction(CommandeReduction $commandeReduction): self { $this->commandeReductions->removeElement($commandeReduction); return $this; }
}