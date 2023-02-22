<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column]
    private ?int $nbInscritMax = null;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Participants::class, mappedBy: 'idSortie')]
    private Collection $idParticipant;

    #[ORM\ManyToOne(inversedBy: 'idOrganisateur')]
    private ?Participants $idOrganisateur = null;

    #[ORM\ManyToOne(inversedBy: 'idSortie')]
    private ?Sites $idSite = null;

    #[ORM\ManyToOne(inversedBy: 'idSortie')]
    private ?Lieux $idLieu = null;

    #[ORM\ManyToOne(inversedBy: 'idSortie')]
    private ?Etats $idEtat = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $motif = null;

    public function __construct()
    {
        $this->idParticipant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNbInscritMax(): ?int
    {
        return $this->nbInscritMax;
    }

    public function setNbInscritMax(int $nbInscritMax): self
    {
        $this->nbInscritMax = $nbInscritMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Participants>
     */
    public function getIdParticipant(): Collection
    {
        return $this->idParticipant;
    }

    public function addIdParticipant(Participants $idParticipant): self
    {
        if (!$this->idParticipant->contains($idParticipant)) {
            $this->idParticipant->add($idParticipant);
            $idParticipant->addIdSortie($this);
        }

        return $this;
    }

    public function removeIdParticipant(Participants $idParticipant): self
    {
        if ($this->idParticipant->removeElement($idParticipant)) {
            $idParticipant->removeIdSortie($this);
        }

        return $this;
    }

    public function getIdOrganisateur(): ?Participants
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?Participants $idOrganisateur): self
    {
        $this->idOrganisateur = $idOrganisateur;

        return $this;
    }

    public function getIdSite(): ?Sites
    {
        return $this->idSite;
    }

    public function setIdSite(?Sites $idSite): self
    {
        $this->idSite = $idSite;

        return $this;
    }

    public function getIdLieu(): ?Lieux
    {
        return $this->idLieu;
    }

    public function setIdLieu(?Lieux $idLieu): self
    {
        $this->idLieu = $idLieu;

        return $this;
    }

    public function getIdEtat(): ?Etats
    {
        return $this->idEtat;
    }

    public function setIdEtat(?Etats $idEtat): self
    {
        $this->idEtat = $idEtat;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

}
