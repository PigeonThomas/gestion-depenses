<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\MagasinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MagasinRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['lien_magasin'], message: 'Il existe déjà un magasin avec ce lien')]

class Magasin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Le nom du magasin ne peut pas être vide')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du magasin ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\NoSuspiciousCharacters(
        invisibleMessage: 'Le nom du magasin ne doit pas contenir de caractères invisibles.',
        mixedNumbersMessage: 'Le nom du magasin ne doit pas mélanger des chiffres de plusieurs alphabets.',
        hiddenOverlayMessage: 'Le nom du magasin contient des caractères interdits.',
        restrictionLevelMessage: 'Le nom du magasin contient des caractères non autorisés.'
    )]
    private string $nom_magasin = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull(message: 'Le statut en ligne du magasin ne peut pas être vide')]
    #[Assert\Type(type: 'bool', message: 'Le statut en ligne du magasin doit être un booléen')]
    private ?bool $online_magasin = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L\'adresse du magasin ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $adresse_magasin = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, unique: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le lien du magasin ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Url(message: 'Le lien du magasin doit être une URL valide')]
    private ?string $lien_magasin = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'magasin')]
    private Collection $depenses;

    #[ORM\ManyToOne(inversedBy: 'magasins')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

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
