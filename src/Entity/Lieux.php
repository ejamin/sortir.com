<?php

namespace App\Entity;

use App\Repository\LieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuxRepository::class)]
class Lieux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $rue = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\OneToMany(mappedBy: 'idLieu', targetEntity: Sorties::class)]
    private Collection $idSortie;

    #[ORM\ManyToOne(inversedBy: 'idLieu')]
    private ?Villes $idVille = null;

    public function __construct()
    {
        $this->idSortie = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

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
            $idSortie->setIdLieu($this);
        }

        return $this;
    }

    public function removeIdSortie(Sorties $idSortie): self
    {
        if ($this->idSortie->removeElement($idSortie)) {
            // set the owning side to null (unless already changed)
            if ($idSortie->getIdLieu() === $this) {
                $idSortie->setIdLieu(null);
            }
        }

        return $this;
    }

    public function getIdVille(): ?Villes
    {
        return $this->idVille;
    }

    public function setIdVille(?Villes $idVille): self
    {
        $this->idVille = $idVille;

        return $this;
    }
}
