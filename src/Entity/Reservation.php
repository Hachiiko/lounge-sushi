<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $numberOfPeople = null;

    #[ORM\ManyToOne]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Table $table = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $beginsAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: ReservationProduct::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $reservationProducts;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmedAt = null;

    public function __construct()
    {
        $this->reservationProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNumberOfPeople(): ?int
    {
        return $this->numberOfPeople;
    }

    public function setNumberOfPeople(?int $numberOfPeople): self
    {
        $this->numberOfPeople = $numberOfPeople;

        return $this;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->beginsAt;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->beginsAt = $date;
        $this->endsAt = (clone $date)->modify('+1 hour');

        return $this;
    }

    public function getBeginsAt(): ?\DateTimeImmutable
    {
        return $this->beginsAt;
    }

    public function setBeginsAt(\DateTimeImmutable $beginsAt): self
    {
        $this->beginsAt = $beginsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * @return Collection<int, ReservationProduct>
     */
    public function getReservationProducts(): Collection
    {
        return $this->reservationProducts;
    }

    public function addReservationProduct(ReservationProduct $reservationProduct): self
    {
        if (!$this->reservationProducts->contains($reservationProduct)) {
            $this->reservationProducts->add($reservationProduct);
            $reservationProduct->setReservation($this);
        }

        return $this;
    }

    public function removeReservationProduct(ReservationProduct $reservationProduct): self
    {
        if ($this->reservationProducts->removeElement($reservationProduct)) {
            // set the owning side to null (unless already changed)
            if ($reservationProduct->getReservation() === $this) {
                $reservationProduct->setReservation(null);
            }
        }

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeImmutable $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getTotalPrice(): int
    {
        return array_sum(
            array_map(
                fn (ReservationProduct $reservationProduct) => $reservationProduct->getProduct()->getPrice(),
                $this->reservationProducts->toArray(),
            ),
        );
    }
}
