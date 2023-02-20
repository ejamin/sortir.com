<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SitesRepository::class)]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'idSites', targetEntity: Participants::class)]
    private Collection $idParticipant;

    #[ORM\OneToMany(mappedBy: 'idSite', targetEntity: Sorties::class)]
    private Collection $idSortie;

    public function __construct()
    {
        $this->idParticipant = new ArrayCollection();
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
            $idParticipant->setIdSites($this);
        }

        return $this;
    }

    public function removeIdParticipant(Participants $idParticipant): self
    {
        if ($this->idParticipant->removeElement($idParticipant)) {
            // set the owning side to null (unless already changed)
            if ($idParticipant->getIdSites() === $this) {
                $idParticipant->setIdSites(null);
            }
        }

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
            $idSortie->setIdSite($this);
        }

        return $this;
    }

    public function removeIdSortie(Sorties $idSortie): self
    {
        if ($this->idSortie->removeElement($idSortie)) {
            // set the owning side to null (unless already changed)
            if ($idSortie->getIdSite() === $this) {
                $idSortie->setIdSite(null);
            }
        }

        return $this;
    }
}
