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

    #[ORM\ManyToMany(targetEntity: Restaurant::class, mappedBy: 'owners')]
    private Collection $ownedRestaurants;

    #[ORM\ManyToMany(targetEntity: Restaurant::class, mappedBy: 'employees')]
    private Collection $workplaceRestaurants;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

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

    /**
     * @return Collection<int, Restaurant>
     */
    public function getOwnedRestaurants(): Collection
    {
        return $this->ownedRestaurants;
    }

    public function addOwnedRestaurant(Restaurant $ownedRestaurant): self
    {
        if (!$this->ownedRestaurants->contains($ownedRestaurant)) {
            $this->ownedRestaurants->add($ownedRestaurant);
            $ownedRestaurant->addOwner($this);
        }

        return $this;
    }

    public function removeOwnedRestaurant(Restaurant $ownedRestaurant): self
    {
        if ($this->ownedRestaurants->removeElement($ownedRestaurant)) {
            $ownedRestaurant->removeOwner($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Restaurant>
     */
    public function getWorkplaceRestaurants(): Collection
    {
        return $this->workplaceRestaurants;
    }

    public function addWorkplaceRestaurant(Restaurant $workplaceRestaurant): self
    {
        if (!$this->workplaceRestaurants->contains($workplaceRestaurant)) {
            $this->workplaceRestaurants->add($workplaceRestaurant);
            $workplaceRestaurant->addEmployee($this);
        }

        return $this;
    }

    public function removeWorkplaceRestaurant(Restaurant $workplaceRestaurant): self
    {
        if ($this->workplaceRestaurants->removeElement($workplaceRestaurant)) {
            $workplaceRestaurant->removeEmployee($this);
        }

        return $this;
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
}
