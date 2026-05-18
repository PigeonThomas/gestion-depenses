<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_depense = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 2)]
    private ?string $montant_depense = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire_depense = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facture_depense = null;

    #[Vich\UploadableField(mapping: "factures", fileNameProperty: "facture_depense")]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['application/pdf', 'image/jpeg', 'image/png'],
        mimeTypesMessage: 'Veuillez télécharger un fichier PDF, JPEG ou PNG valide.',
    )]
    private ?File $factureFile = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $km_vehicule = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 3, nullable: true)]
    private ?string $carbu_prix_litre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repa_description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repa_ref_pieces = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repa_photos = null;

    #[Vich\UploadableField(mapping: "reparations", fileNameProperty: "repa_photos")]
    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPG ou PNG).',
    )]
    private ?File $repaPhotosFile = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?Magasin $magasin = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?Vehicule $vehicule = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepense(): ?\DateTime
    {
        return $this->date_depense;
    }

    public function setDateDepense(\DateTime $date_depense): static
    {
        $this->date_depense = $date_depense;

        return $this;
    }

    public function getMontantDepense(): ?string
    {
        return $this->montant_depense;
    }

    public function setMontantDepense(string $montant_depense): static
    {
        $this->montant_depense = $montant_depense;

        return $this;
    }

    public function getCommentaireDepense(): ?string
    {
        return $this->commentaire_depense;
    }

    public function setCommentaireDepense(?string $commentaire_depense): static
    {
        $this->commentaire_depense = $commentaire_depense;

        return $this;
    }

    public function getFactureDepense(): ?string
    {
        return $this->facture_depense;
    }

    public function setFactureDepense(?string $facture_depense): static
    {
        $this->facture_depense = $facture_depense;

        return $this;
    }

    public function getKmVehicule(): ?string
    {
        return $this->km_vehicule;
    }

    public function setKmVehicule(?string $km_vehicule): static
    {
        $this->km_vehicule = $km_vehicule;

        return $this;
    }

    public function getCarbuPrixLitre(): ?string
    {
        return $this->carbu_prix_litre;
    }

    public function setCarbuPrixLitre(?string $carbu_prix_litre): static
    {
        $this->carbu_prix_litre = $carbu_prix_litre;

        return $this;
    }

    public function getRepaDescription(): ?string
    {
        return $this->repa_description;
    }

    public function setRepaDescription(?string $repa_description): static
    {
        $this->repa_description = $repa_description;

        return $this;
    }

    public function getRepaRefPieces(): ?string
    {
        return $this->repa_ref_pieces;
    }

    public function setRepaRefPieces(?string $repa_ref_pieces): static
    {
        $this->repa_ref_pieces = $repa_ref_pieces;

        return $this;
    }

    public function getRepaPhotos(): ?string
    {
        return $this->repa_photos;
    }

    public function setRepaPhotos(?string $repa_photos): static
    {
        $this->repa_photos = $repa_photos;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): static
    {
        $this->magasin = $magasin;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

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
     * Get the value of factureFile
     */ 
    public function getFactureFile()
    {
        return $this->factureFile;
    }

    /**
     * Set the value of factureFile
     *
     * @return  static
     */ 
    public function setFactureFile(?File $factureFile): static
    {
        $this->factureFile = $factureFile;

        if ($factureFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getRepaPhotosFile(): ?File
    {
        return $this->repaPhotosFile;
    }

    public function setRepaPhotosFile(?File $repaPhotosFile): static
    {
        $this->repaPhotosFile = $repaPhotosFile;

        if ($repaPhotosFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function updateTimestampOnCreate(): void
    {
        $this->updatedAt ??= new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestampOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
