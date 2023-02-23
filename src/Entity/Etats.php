<?php

namespace App\Entity;

use App\Repository\EtatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatsRepository::class)]
class Etats
{
    //const ETAT_PUBLIE = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'idEtat', targetEntity: Sorties::class)]
    private Collection $idSortie;

    public function __construct()
    {
        $this->idSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $idSortie->setIdEtat($this);
        }

        return $this;
    }

    public function removeIdSortie(Sorties $idSortie): self
    {
        if ($this->idSortie->removeElement($idSortie)) {
            // set the owning side to null (unless already changed)
            if ($idSortie->getIdEtat() === $this) {
                $idSortie->setIdEtat(null);
            }
        }

        return $this;
    }
}
