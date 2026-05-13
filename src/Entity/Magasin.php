<?php

namespace App\Entity;

use App\Repository\MagasinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MagasinRepository::class)]
class Magasin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_magasin = null;

    #[ORM\Column]
    private ?bool $online_magasin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_magasin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien_magasin = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'magasin')]
    private Collection $depenses;

    public function __construct()
    {
        $this->depenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMagasin(): ?string
    {
        return $this->nom_magasin;
    }

    public function setNomMagasin(string $nom_magasin): static
    {
        $this->nom_magasin = $nom_magasin;

        return $this;
    }

    public function isOnlineMagasin(): ?bool
    {
        return $this->online_magasin;
    }

    public function setOnlineMagasin(bool $online_magasin): static
    {
        $this->online_magasin = $online_magasin;

        return $this;
    }

    public function getAdresseMagasin(): ?string
    {
        return $this->adresse_magasin;
    }

    public function setAdresseMagasin(?string $adresse_magasin): static
    {
        $this->adresse_magasin = $adresse_magasin;

        return $this;
    }

    public function getLienMagasin(): ?string
    {
        return $this->lien_magasin;
    }

    public function setLienMagasin(?string $lien_magasin): static
    {
        $this->lien_magasin = $lien_magasin;

        return $this;
    }

    /**
     * @return Collection<int, Depense>
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): static
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses->add($depense);
            $depense->setMagasin($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getMagasin() === $this) {
                $depense->setMagasin(null);
            }
        }

        return $this;
    }
}
