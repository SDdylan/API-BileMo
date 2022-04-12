<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Storage;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //création d'un type de produit et d'une marque :
        $type = new Type();
        $type->setName('phone');
        $manager->persist($type);

        $brand = new Brand();
        $brand->setName('Apple');
        $manager->persist($brand);

        //création des produits
//        $productData = [
//          [
//            'IPhone 8',
//            'smartphone tournant sous iOS 12',
//
//          ],
//          [
//
//          ]
//        ];

        $iphone = new Product();
        $iphone->setType($type)
            ->setDescription('Smartphone sous iOS 12.')
            ->setBrand($brand)
            ->setModel('iPhone 8')
            ->setReleaseDate(new \DateTime("2017-09-22"));
        $manager->persist($iphone);

        $iphone2 = new Product();
        $iphone2->setType($type)
            ->setDescription('Smartphone sous iOS 12, modèle premium.')
            ->setBrand($brand)
            ->setModel('iPhone 8 Plus')
            ->setReleaseDate(new \DateTime("2017-09-22"));
        $manager->persist($iphone2);

        $storage1 = new Storage();
        $storage1->setCapacity('64')
            ->setColor('black')
            ->setPrice(809.00)
            ->setProduct($iphone);
        $manager->persist($storage1);

        $storage2 = new Storage();
        $storage2->setCapacity('128')
            ->setColor('black')
            ->setPrice(979.00)
            ->setProduct($iphone);
        $manager->persist($storage2);

        $storage3 = new Storage();
        $storage3->setCapacity('64')
            ->setColor('black')
            ->setPrice(919.00)
            ->setProduct($iphone2);
        $manager->persist($storage3);

        $storage4 = new Storage();
        $storage4->setCapacity('128')
            ->setColor('black')
            ->setPrice(1089.00)
            ->setProduct($iphone2);
        $manager->persist($storage4);

        $manager->flush();
    }
}
