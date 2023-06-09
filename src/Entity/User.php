<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';
    public const ROLE_OWNER = 'ROLE_OWNER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Restaurant $ownedRestaurant = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    private ?Restaurant $workplaceRestaurant = null;

    public function __construct()
    {
        $this->ownedRestaurants = new ArrayCollection();
        $this->workplaceRestaurants = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s %s - %s', $this->firstName, $this->lastName, $this->email);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOwnedRestaurant(): ?Restaurant
    {
        return $this->ownedRestaurant;
    }

    public function setOwnedRestaurant(Restaurant $ownedRestaurant): self
    {
        // set the owning side of the relation if necessary
        if ($ownedRestaurant->getOwner() !== $this) {
            $ownedRestaurant->setOwner($this);
        }

        $this->ownedRestaurant = $ownedRestaurant;

        return $this;
    }

    public function getWorkplaceRestaurant(): ?Restaurant
    {
        return $this->workplaceRestaurant;
    }

    public function setWorkplaceRestaurant(?Restaurant $workplaceRestaurant): self
    {
        $this->workplaceRestaurant = $workplaceRestaurant;

        return $this;
    }
}
