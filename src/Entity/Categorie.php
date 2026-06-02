<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['nom_categorie'], message: 'Il existe déjà une catégorie avec ce nom')]
#[UniqueEntity(fields: ['couleur_categorie'], message: 'Il existe déjà une catégorie avec cette couleur')]

class Categorie
{
    public const ICON_CHOICES = [
        'Pain' => 'fa-solid fa-bread-slice',
        'Boissons' => 'fa-solid fa-champagne-glasses',
        'Courses' => 'fas fa-shopping-cart',
        'Maison' => 'fas fa-home',
        'Sante' => 'fas fa-heart',
        'Loisirs' => 'fas fa-film',
        'Restaurant' => 'fas fa-utensils',
        'Voiture' => 'fas fa-car',
        'Velo' => 'fas fa-bicycle',
        'Bus' => 'fas fa-bus',
        'Panier' => 'fa-solid fa-basket-shopping',
        'Reparation' => 'fa-solid fa-wrench',
        'Carburant' => 'fa-solid fa-gas-pump',
        'Achats' => 'fa-solid fa-bag-shopping',
        'Train' => 'fa-solid fa-train',
        'Tabac' => 'fa-solid fa-smoking',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le nom de la catégorie ne peut pas être vide')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\NoSuspiciousCharacters(
        invisibleMessage: 'Le nom de la catégorie ne doit pas contenir de caractères invisibles.',
        mixedNumbersMessage: 'Le nom de la catégorie ne doit pas mélanger des chiffres de plusieurs alphabets.',
        hiddenOverlayMessage: 'Le nom de la catégorie contient des caractères interdits.',
        restrictionLevelMessage: 'Le nom de la catégorie contient des caractères non autorisés.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}\p{N}\s\-]+$/u',
        message: 'Le nom de la catégorie ne peut contenir que des lettres, des chiffres, des espaces et des tirets'
    )]  
    private string $nom_categorie = '';

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'La couleur de la catégorie ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
        message: 'La couleur doit être au format hexadécimal (ex: #FF5733)'
    )]
    #[Assert\NotNull(message: 'La couleur de la catégorie ne peut pas être vide')]
    private string $couleur_categorie = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(
        choices: self::ICON_CHOICES,
        message: 'L\'icône sélectionnée est invalide'
    )]
    private ?string $icone_categorie = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'categorie')]
    private Collection $depenses;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->depenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(string $nom_categorie): static
    {
        $this->nom_categorie = trim($nom_categorie);

        return $this;
    }

    public function getCouleurCategorie(): ?string
    {
        return $this->couleur_categorie;
    }

    public function setCouleurCategorie(string $couleur_categorie): static
    {
        $this->couleur_categorie = strtoupper(trim($couleur_categorie));

        return $this;
    }

    public function getIconeCategorie(): ?string
    {
        return $this->icone_categorie;
    }

    public function getIconeCategorieClass(): ?string
    {
        if ($this->icone_categorie === null) {
            return null;
        }

        if (preg_match('/class="([^"]+)"/', $this->icone_categorie, $matches) === 1) {
            return $matches[1];
        }

        return $this->icone_categorie;
    }

    public function setIconeCategorie(?string $icone_categorie): static
    {
        if ($icone_categorie !== null && preg_match('/class="([^"]+)"/', $icone_categorie, $matches) === 1) {
            $icone_categorie = $matches[1];
        }

        $this->icone_categorie = $icone_categorie;

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
            $depense->setCategorie($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getCategorie() === $this) {
                $depense->setCategorie(null);
            }
        }

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
}
