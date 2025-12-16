<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use App\Entity\Menu;

#[ORM\Entity]
#[ORM\Table(name:"favori")]
#[ORM\UniqueConstraint(name:"uniq_utilisateur_menu", columns:["utilisateur_id", "menu_id"])]
#[ORM\HasLifecycleCallbacks]
class Favori
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateAjout = null;

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function getMenu(): ?Menu { return $this->menu; }
    public function getDateAjout(): ?\DateTimeInterface { return $this->dateAjout; }

    // Setters
    public function setUtilisateur(?Utilisateur $utilisateur): self { $this->utilisateur = $utilisateur; return $this; }
    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }
    public function setDateAjout(\DateTimeInterface $dateAjout): self { $this->dateAjout = $dateAjout; return $this; }

    // Lifecycle callback pour remplir la date automatiquement
    #[ORM\PrePersist]
    public function setDateAjoutAutomatically(): void {
        if ($this->dateAjout === null) {
            $this->dateAjout = new \DateTimeImmutable();
        }
    }
}