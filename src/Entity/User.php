<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_user = null;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'user')]
    private Collection $depenses;

    /**
     * @var Collection<int, Magasin>
     */
    #[ORM\OneToMany(targetEntity: Magasin::class, mappedBy: 'user')]
    private Collection $magasins;

    /**
     * @var Collection<int, Vehicule>
     */
    #[ORM\OneToMany(targetEntity: Vehicule::class, mappedBy: 'user')]
    private Collection $vehicules;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_user = null;

    #[Vich\UploadableField(mapping: "users", fileNameProperty: "image_user")]
    private ?File $imageUserFile = null;


    public function __construct()
    {
        $this->depenses = new ArrayCollection();
        $this->magasins = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     * it is important to serialize the minimal set of properties needed to identify the user and their roles, while excluding sensitive information like the actual password hash.
     * This approach allows the session to be secure while still enabling the necessary functionality for user authentication and authorization. 
     * no need to serialize profile picture or other non-essential data, as it can be retrieved from the database when needed, ensuring that the session remains lightweight and secure.
    */
    public function __serialize(): array
    {
        return [
        'id' => $this->id,
        'email' => $this->email,
        'roles' => $this->roles,
        'password' => hash('crc32c', (string) $this->password),
    ];
    }
    
    /**
     * The __unserialize method should be implemented to restore the state of the object from the serialized data. 
     * It should assign the values from the $data array back to the corresponding properties of the User object. 
     * This allows the session to correctly restore the user's identity and roles when they log in, while ensuring that sensitive information like the actual password hash is not stored in the session.
    */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->roles = $data['roles'] ?? [];
        $this->password = $data['password'] ?? null;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): static
    {
        $this->nom_user = $nom_user;

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenom_user;
    }

    public function setPrenomUser(?string $prenom_user): static
    {
        $this->prenom_user = $prenom_user;

        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresse_user;
    }

    public function setAdresseUser(?string $adresse_user): static
    {
        $this->adresse_user = $adresse_user;

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
            $depense->setUser($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getUser() === $this) {
                $depense->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Magasin>
     */
    public function getMagasins(): Collection
    {
        return $this->magasins;
    }

    public function addMagasin(Magasin $magasin): static
    {
        if (!$this->magasins->contains($magasin)) {
            $this->magasins->add($magasin);
            $magasin->setUser($this);
        }

        return $this;
    }

    public function removeMagasin(Magasin $magasin): static
    {
        if ($this->magasins->removeElement($magasin)) {
            if ($magasin->getUser() === $this) {
                $magasin->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setUser($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            if ($vehicule->getUser() === $this) {
                $vehicule->setUser(null);
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

    public function getImageUser(): ?string
    {
        return $this->image_user;
    }

    public function setImageUser(?string $image_user): static
    {
        $this->image_user = $image_user;

        return $this;
    }

    /**
     * Get the value of imageUserFile
     */ 
    public function getImageUserFile()
    {
        return $this->imageUserFile;
    }

    /**
     * Set the value of imageUserFile
     *
     * @return  static
     */ 
    public function setImageUserFile(?File $imageUserFile): static
    {
        $this->imageUserFile = $imageUserFile;

        if ($imageUserFile !== null) {
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
}
