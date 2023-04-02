<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TableFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $restaurants = $manager->getRepository(Restaurant::class)->findAll();

        foreach ($restaurants as $restaurant) {
            for ($i = 1; $i <= 5; $i++) {
                $table = (new Table)
                    ->setRestaurant($restaurant)
                    ->setName('Stolik ' . $i);

                $manager->persist($table);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
        ];
    }
}
