<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    private array $restaurantProducts = [
        'yume-sushi' => [
            [
                'name' => 'Zestaw Rambo 54 Szt.',
                'description' => 'Nigiri łosoś 4 szt., Hoso ogórek 8 szt., Hoso łosoś wędzony 8 szt., Futo łosoś wędzony 12 szt. + GRATIS: Star crab 8 szt., Futo Butter Gold 6 szt. ',
                'price' => 14900,
                'image' => 'zestaw-rambo-54-szt.jpg'
            ], [
                'name' => 'Zestaw Godzilla 40 Szt. + 8 Gratis!',
                'description' => 'Futo łosoś wędzony z mango - cała rolka w tempurze 12 szt., hosomaki ogórek 8 szt., hosomaki łosoś wędzony 8 szt., Futo Ebi Ten ostry 6 szt., Futo sałatka surimi - 6 szt. + gratis California Ebi Ten 8 szt.',
                'price' => 13900,
                'image' => 'zestaw-godzilla-40-szt-8-gratis.jpg',
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        (new Filesystem)->mirror(
            $this->parameterBag->get('kernel.project_dir') . '/fixtures/uploads/products/',
            $this->parameterBag->get('kernel.project_dir') . '/public/uploads/products/',
        );

        foreach ($this->restaurantProducts as $restaurantSlug => $productsData) {
            $restaurant = $manager->getRepository(Restaurant::class)->findOneBy(['slug' => $restaurantSlug]);

            foreach ($productsData as $productData) {
                $product = (new Product)
                    ->setRestaurant($restaurant)
                    ->setName($productData['name'])
                    ->setDescription($productData['description'])
                    ->setPrice($productData['price'])
                    ->setImage($productData['image']);

                $manager->persist($product);
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
