<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Client;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $client = new Client();
        $clearPassword = '123456789';

        $client->setName($faker->name())
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setEmail($faker->email())

            ->encodePassword($clearPassword);
        $manager->persist($client);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword($faker->password(6, 8))
                ->setRoles(['ROLE_USER'])
                ->setClient($client);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
