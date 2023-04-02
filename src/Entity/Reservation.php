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

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
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
        return $this->table?->getRestaurant();
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
