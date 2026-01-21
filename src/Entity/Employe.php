<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
class Employe implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $nom = null;

    #[ORM\Column(type:"string", length:50)]
    private ?string $prenom = null;

    #[ORM\Column(type:"string", length:100, unique:true)]
    private ?string $email = null;

    #[ORM\Column(type:"string")]
    private ?string $motDePasse = null;

    #[ORM\Column(type:"boolean")] 
    private bool $actif = true;

    #[ORM\Column(type:"string", length:20, nullable:true)]
    private ?string $telephone = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    private ?Role $role = null;

    // ----- Getters / setters -----
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function getEmail(): ?string { return $this->email; }
    public function getPassword(): ?string { return $this->motDePasse; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function isActif(): bool { return $this->actif; }
    public function getRole(): ?Role { return $this->role; }

    public function setNom(string $n): self { $this->nom = $n; return $this; }
    public function setPrenom(string $p): self { $this->prenom = $p; return $this; }
    public function setEmail(string $e): self { $this->email = $e; return $this; }
    public function setPassword(string $pass): self { $this->motDePasse = $pass; return $this; }
    public function setTelephone(?string $t): self { $this->telephone = $t; return $this; }
    public function setActif(bool $a): self { $this->actif = $a; return $this; }
    public function setRole(?Role $role): self { $this->role = $role; return $this; }

    // ----- UserInterface -----
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        if (!$this->role) {
            return ['ROLE_EMPLOYE'];
        }
        $roleLibelle = strtoupper($this->role->getLibelle()); // Admin -> ADMIN
        $roles = ["ROLE_$roleLibelle"];

        // Si l’utilisateur est Admin, il est aussi Employé
        if ($roleLibelle === 'ADMIN') {
            $roles[] = 'ROLE_EMPLOYE';
        }

        return $roles;
    }
    
    public function eraseCredentials(): void {}
}