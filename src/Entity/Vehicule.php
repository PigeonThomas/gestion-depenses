<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['immat_vehicule'], message: 'Il existe déjà un véhicule avec cette immatriculation')]

class Vehicule
{
    public const TYPE_CHOICES = [
        'Voiture' => 'voiture',
        'Moto' => 'moto',
        'Camion' => 'camion',
        'Van' => 'van',
        'Camping-car' => 'camping-car',
        'Vélo' => 'velo',
        'Trottinette' => 'trottinette',
        'Bus' => 'bus',
        'Hélicoptère' => 'helicoptere',
        'Autre' => 'autre',
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L\'immatriculation du véhicule ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}\p{N}\s\-]+$/u',
        message: 'L\'immatriculation ne doit contenir que des lettres, chiffres, tirets et espaces'
    )]
    #[Assert\NoSuspiciousCharacters(
        invisibleMessage: 'L\'immatriculation ne doit pas contenir de caractères invisibles.',
        mixedNumbersMessage: 'L\'immatriculation ne doit pas mélanger des chiffres de plusieurs alphabets.',
        hiddenOverlayMessage: 'L\'immatriculation contient des caractères interdits.',
        restrictionLevelMessage: 'L\'immatriculation contient des caractères non autorisés.'
    )]
    private ?string $immat_vehicule = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Le surnom du véhicule ne peut pas être vide')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le surnom du véhicule ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\NoSuspiciousCharacters(
        invisibleMessage: 'Le surnom du véhicule ne doit pas contenir de caractères invisibles.',
        mixedNumbersMessage: 'Le surnom du véhicule ne doit pas mélanger des chiffres de plusieurs alphabets.',
        hiddenOverlayMessage: 'Le surnom du véhicule contient des caractères interdits.',
        restrictionLevelMessage: 'Le surnom du véhicule contient des caractères non autorisés.'
    )]
    private string $surnom_vehicule = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Le type du véhicule ne peut pas être vide')]
    #[Assert\Choice(choices: self::TYPE_CHOICES, message: 'Le type de véhicule sélectionné est invalide')]
    private string $type_vehicule = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La marque du véhicule ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\NoSuspiciousCharacters(
        invisibleMessage: 'La marque du véhicule ne doit pas contenir de caractères invisibles.',
        mixedNumbersMessage: 'La marque du véhicule ne doit pas mélanger des chiffres de plusieurs  alphabets.',
        hiddenOverlayMessage: 'La marque du véhicule contient des caractères interdits.',
        restrictionLevelMessage: 'La marque du véhicule contient des caractères non autorisés.'
    )]
    private ?string $marque_vehicule = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le modèle du véhicule ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $modele_vehicule = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual('today', message: 'L\'année de circulation du véhicule ne peut pas être dans le futur')]
    private ?\DateTimeImmutable $annee_circulation_vehicule = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L\'énergie du véhicule ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $energie_vehicule = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 0, nullable: true)]
    #[Assert\Positive(message: 'Le kilométrage à l\'achat doit être un nombre positif')]
    #[Assert\Type(type: 'numeric', message: 'Le kilométrage à l\'achat doit être un nombre valide')]
    private ?string $kmAchat_vehicule = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image_vehicule = null;

    #[Vich\UploadableField(mapping: "vehicules", fileNameProperty: "image_vehicule")]
    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPG ou PNG)',
    )]
    private ?File $imageVehiculeFile = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'vehicule')]
    private Collection $depenses;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 0, nullable: true)]
    private ?string $km_vidange = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 0, nullable: true)]
    private ?string $km_distribution = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 1, scale: 0, nullable: true)]
    private ?string $annee_distribution = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Choice(choices: [1, 2, 5], message: 'La périodicité du contrôle technique doit être 1, 2 ou 5 ans')]
    private ?int $annee_controle_technique = null;

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

    public function getAnneeCirculationVehicule(): ?\DateTimeImmutable
    {
        return $this->annee_circulation_vehicule;
    }

    public function setAnneeCirculationVehicule(?\DateTimeImmutable $annee_circulation_vehicule): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of imageVehiculeFile
     */ 
    public function getImageVehiculeFile()
    {
        return $this->imageVehiculeFile;
    }

    /**
     * Set the value of imageVehiculeFile
     *
     * @return  static
     */ 
    public function setImageVehiculeFile(?File $imageVehiculeFile): static
    {
        $this->imageVehiculeFile = $imageVehiculeFile;

        if ($imageVehiculeFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function updateTimestampsOnCreate(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt ??= $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function updateTimestampOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getKmVidange(): ?string
    {
        return $this->km_vidange;
    }

    public function setKmVidange(?string $km_vidange): static
    {
        $this->km_vidange = $km_vidange;

        return $this;
    }

    public function getKmDistribution(): ?string
    {
        return $this->km_distribution;
    }

    public function setKmDistribution(?string $km_distribution): static
    {
        $this->km_distribution = $km_distribution;

        return $this;
    }

    public function getAnneeDistribution(): ?string
    {
        return $this->annee_distribution;
    }

    public function setAnneeDistribution(?string $annee_distribution): static
    {
        $this->annee_distribution = $annee_distribution;

        return $this;
    }

    public function getAnneeControleTechnique(): ?int
    {
        return $this->annee_controle_technique;
    }

    public function setAnneeControleTechnique(?int $annee_controle_technique): static
    {
        $this->annee_controle_technique = $annee_controle_technique;

        return $this;
    }
}
