<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class RestaurantFixtures extends Fixture
{
    private array $restaurantsData = [
        [
            'name' => 'Yume Sushi',
            'description' => 'Sushi z dowozem na terenie Warszawy i najbliższych okolic. Staramy się, aby każdy mógł znaleźć tutaj coś dla siebie.',
            'slug' => 'yume-sushi',
            'address' => 'Warszawska 58C',
            'postcode' => '02-496',
            'city' => 'Warszawa',
            'phone' => '+48570070757',
            'logo' => 'yume-sushi.jpg',
        ],
    ];

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
        (new Filesystem)->mirror(
            $this->parameterBag->get('kernel.project_dir') . '/fixtures/uploads/restaurants/',
            $this->parameterBag->get('kernel.project_dir') . '/public/uploads/restaurants/',
        );

        foreach ($this->restaurantsData as $restaurantData) {
            $restaurant = (new Restaurant)
                ->setName($restaurantData['name'])
                ->setDescription($restaurantData['description'])
                ->setSlug($restaurantData['slug'])
                ->setAddress($restaurantData['address'])
                ->setPostcode($restaurantData['postcode'])
                ->setCity($restaurantData['city'])
                ->setPhone($restaurantData['phone'])
                ->setLogo($restaurantData['logo']);

            $manager->persist($restaurant);
        }

        $manager->flush();
    }
}
