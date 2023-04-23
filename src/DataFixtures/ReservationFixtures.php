<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\ReservationProduct;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $restaurants = $manager->getRepository(Restaurant::class)->findAll();

        $firstNames = [
            'Jan',
            'Maciej',
            'Andrzej',
            'Stanisław',
            'Piotr',
        ];

        $lastNames = [
            'Kowalski',
            'Nowak',
            'Wiśniewski',
            'Dąbrowski',
            'Lewandowski',
        ];

        foreach (range(1, 12) as $month) {
            foreach ($restaurants as $restaurant) {
                $tables = $restaurant->getTables()->toArray();
                $tablesCount = $restaurant->getTables()->count();

                $randomTableIndexes = array_rand($tables, random_int(1, $tablesCount));

                if (is_int($randomTableIndexes)) {
                    $randomTableIndexes = [$randomTableIndexes];
                }

                foreach ($randomTableIndexes as $tableIndex) {
                    $table = $tables[$tableIndex];

                    $date = (new \DateTimeImmutable)
                        ->setDate((int) date('Y'), $month, random_int(1, 20));

                    $reservation = (new Reservation)
                        ->setName($firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)])
                        ->setPhone('+48 123 123 123')
                        ->setNumberOfPeople(random_int(1, 10))
                        ->setTable($table)
                        ->setBeginsAt($date)
                        ->setEndsAt($date->modify('+1 hour'))
                        ->setConfirmedAt(random_int(0, 1) ? $date : null);

                    $products = $restaurant->getProducts()->toArray();
                    $productsCount = $restaurant->getProducts()->count();

                    $randomProductIndexes = array_rand($products, random_int(1, $productsCount));

                    if (is_int($randomProductIndexes)) {
                        $randomProductIndexes = [$randomProductIndexes];
                    }

                    foreach ($randomProductIndexes as $productIndex) {
                        $product = $products[$productIndex];

                        $reservationProduct = (new ReservationProduct)
                            ->setReservation($reservation)
                            ->setProduct($product)
                            ->setQuantity(random_int(1, 3));

                        $manager->persist($reservationProduct);
                    }

                    $manager->persist($reservation);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
            TableFixtures::class,
            ProductFixtures::class,
        ];
    }
}
