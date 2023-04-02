<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public function setUserPasswordHasher(UserPasswordHasherInterface $userPasswordHasher): void
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
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

        $password = $this->userPasswordHasher->hashPassword(new User(), '123');

        $admin = (new User())
            ->setFirstName($firstNames[array_rand($firstNames)])
            ->setLastName($lastNames[array_rand($lastNames)])
            ->setEmail('admin@example.com')
            ->setPassword($password)
            ->setRoles([User::ROLE_ADMIN])
        ;

        $manager->persist($admin);

        $restaurants = $manager->getRepository(Restaurant::class)->findAll();

        foreach ($restaurants as $restaurant) {
            $owner = (new User())
                ->setFirstName($firstNames[array_rand($firstNames)])
                ->setLastName($lastNames[array_rand($lastNames)])
                ->setEmail($restaurant->getSlug() . '-owner@example.com')
                ->setPassword($password)
                ->setRoles([User::ROLE_OWNER])
            ;

            $restaurant->addOwner($owner);

            for ($i = 1; $i <= 5; $i++) {
                $employee = (new User())
                    ->setFirstName($firstNames[array_rand($firstNames)])
                    ->setLastName($lastNames[array_rand($lastNames)])
                    ->setEmail($restaurant->getSlug() . '-employee' . $i . '@example.com')
                    ->setPassword($password)
                    ->setRoles([User::ROLE_EMPLOYEE])
                ;

                $restaurant->addEmployee($employee);

                $manager->persist($employee);
            }

            $manager->persist($owner);
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