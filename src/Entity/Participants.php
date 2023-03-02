<?php

namespace App\Entity;

use App\Repository\ParticipantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert; 

#[ORM\Entity(repositoryClass: ParticipantsRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Participants implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email( message: 'The email {{ value }} is not a valid email.')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: '/^[A-Za-z]+$/', match : true, message : 'Le nom {{ value }} n\'est pas valide, il ne doit contenir que des caractères alphabétiques')]    
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $isAdmin = null;

    #[ORM\Column]
    private ?bool $isActif = null;

    #[ORM\ManyToMany(targetEntity: Sorties::class, inversedBy: 'idParticipant')]
    private Collection $idSortie;

    #[ORM\OneToMany(mappedBy: 'idOrganisateur', targetEntity: Sorties::class)]
    private Collection $idOrganisateur;

    #[ORM\ManyToOne(inversedBy: 'idParticipant')]
    private ?Sites $idSites = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Groupe::class, mappedBy: 'idParticipant')]
    private Collection $idGroupe;

    public function __construct()
    {
        $this->idSortie = new ArrayCollection();
        $this->idOrganisateur = new ArrayCollection();
        $this->idGroupe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getIdSortie(): Collection
    {
        return $this->idSortie;
    }

    public function addIdSortie(Sorties $idSortie): self
    {
        if (!$this->idSortie->contains($idSortie)) {
            $this->idSortie->add($idSortie);
        }

        return $this;
    }

    public function removeIdSortie(Sorties $idSortie): self
    {
        $this->idSortie->removeElement($idSortie);

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getIdOrganisateur(): Collection
    {
        return $this->idOrganisateur;
    }

    public function addIdOrganisateur(Sorties $idOrganisateur): self
    {
        if (!$this->idOrganisateur->contains($idOrganisateur)) {
            $this->idOrganisateur->add($idOrganisateur);
            $idOrganisateur->setIdOrganisateur($this);
        }

        return $this;
    }

    public function removeIdOrganisateur(Sorties $idOrganisateur): self
    {
        if ($this->idOrganisateur->removeElement($idOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($idOrganisateur->getIdOrganisateur() === $this) {
                $idOrganisateur->setIdOrganisateur(null);
            }
        }

        return $this;
    }

    public function getIdSites(): ?Sites
    {
        return $this->idSites;
    }

    public function setIdSites(?Sites $idSites): self
    {
        $this->idSites = $idSites;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getIdGroupe(): Collection
    {
        return $this->idGroupe;
    }

    public function addIdGroupe(Groupe $idGroupe): self
    {
        if (!$this->idGroupe->contains($idGroupe)) {
            $this->idGroupe->add($idGroupe);
            $idGroupe->addIdParticipant($this);
        }

        return $this;
    }

    public function removeIdGroupe(Groupe $idGroupe): self
    {
        if ($this->idGroupe->removeElement($idGroupe)) {
            $idGroupe->removeIdParticipant($this);
        }

        return $this;
    }

    public function hasRoles($role): bool {
        $hasRole = false;
        if(in_array($role,$this->roles)){
            $hasRole = true;
        }
        return $hasRole;
    }
}
