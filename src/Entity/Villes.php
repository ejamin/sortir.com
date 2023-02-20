<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VillesRepository::class)]
class Villes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 10)]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'idVille', targetEntity: Lieux::class)]
    private Collection $idLieu;

    public function __construct()
    {
        $this->idLieu = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieux>
     */
    public function getIdLieu(): Collection
    {
        return $this->idLieu;
    }

    public function addIdLieu(Lieux $idLieu): self
    {
        if (!$this->idLieu->contains($idLieu)) {
            $this->idLieu->add($idLieu);
            $idLieu->setIdVille($this);
        }

        return $this;
    }

    public function removeIdLieu(Lieux $idLieu): self
    {
        if ($this->idLieu->removeElement($idLieu)) {
            // set the owning side to null (unless already changed)
            if ($idLieu->getIdVille() === $this) {
                $idLieu->setIdVille(null);
            }
        }

        return $this;
    }
}
