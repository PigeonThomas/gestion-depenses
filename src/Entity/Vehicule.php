<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $immat_vehicule = null;

    #[ORM\Column(length: 255)]
    private ?string $surnom_vehicule = null;

    #[ORM\Column(length: 255)]
    private ?string $type_vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marque_vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modele_vehicule = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $annee_circulation_vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $energie_vehicule = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 0, nullable: true)]
    private ?string $kmAchat_vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_vehicule = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'vehicule')]
    private Collection $depenses;

    public function __construct()
    {
        $this->depenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatVehicule(): ?string
    {
        return $this->immat_vehicule;
    }

    public function setImmatVehicule(?string $immat_vehicule): static
    {
        $this->immat_vehicule = $immat_vehicule;

        return $this;
    }

    public function getSurnomVehicule(): ?string
    {
        return $this->surnom_vehicule;
    }

    public function setSurnomVehicule(string $surnom_vehicule): static
    {
        $this->surnom_vehicule = $surnom_vehicule;

        return $this;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->type_vehicule;
    }

    public function setTypeVehicule(string $type_vehicule): static
    {
        $this->type_vehicule = $type_vehicule;

        return $this;
    }

    public function getMarqueVehicule(): ?string
    {
        return $this->marque_vehicule;
    }

    public function setMarqueVehicule(?string $marque_vehicule): static
    {
        $this->marque_vehicule = $marque_vehicule;

        return $this;
    }

    public function getModeleVehicule(): ?string
    {
        return $this->modele_vehicule;
    }

    public function setModeleVehicule(?string $modele_vehicule): static
    {
        $this->modele_vehicule = $modele_vehicule;

        return $this;
    }

    public function getAnneeCirculationVehicule(): ?\DateTime
    {
        return $this->annee_circulation_vehicule;
    }

    public function setAnneeCirculationVehicule(?\DateTime $annee_circulation_vehicule): static
    {
        $this->annee_circulation_vehicule = $annee_circulation_vehicule;

        return $this;
    }

    public function getEnergieVehicule(): ?string
    {
        return $this->energie_vehicule;
    }

    public function setEnergieVehicule(?string $energie_vehicule): static
    {
        $this->energie_vehicule = $energie_vehicule;

        return $this;
    }

    public function getKmAchatVehicule(): ?string
    {
        return $this->kmAchat_vehicule;
    }

    public function setKmAchatVehicule(?string $kmAchat_vehicule): static
    {
        $this->kmAchat_vehicule = $kmAchat_vehicule;

        return $this;
    }

    public function getImageVehicule(): ?string
    {
        return $this->image_vehicule;
    }

    public function setImageVehicule(?string $image_vehicule): static
    {
        $this->image_vehicule = $image_vehicule;

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
            $depense->setVehicule($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getVehicule() === $this) {
                $depense->setVehicule(null);
            }
        }

        return $this;
    }
}
