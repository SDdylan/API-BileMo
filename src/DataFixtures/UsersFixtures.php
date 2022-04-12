<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Client;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create();

        $client = new Client();
        $client->setName($faker->name())
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setEmail($faker->email())
            ->setPassword($faker->password(6, 8));
        $manager->persist($client);

        for($i=0; $i<5; $i++){
            $user = new User();
            $user->setEmail($faker->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword($faker->password(6,8))
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
