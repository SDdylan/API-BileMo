<?php

namespace App\DataFixtures;

use App\Entity\Product;
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

        //création des produits
        $productArrayData = [
            [
                'iPhone 12',
                'Smartphone sous iOS 12',
                new \DateTime("2020-10-23"),
                'Apple',
                $type,
                909.00,
                1000,
                true
            ],
            [
                'iPhone 12 PRO',
                'Smartphone sous iOS 12, version PRO',
                new \DateTime("2020-10-23"),
                'Apple',
                $type,
                1159.00,
                500,
                true
            ],
            [
                'iPhone 12 PRO MAX',
                'Smartphone sous iOS 12, version premium MAX',
                new \DateTime("2020-10-23"),
                'Apple',
                $type,
                1259.00,
                500,
                true
            ]
        ];

        $nbProduct = count($productArrayData);
        //Creation de tricks
        for ($i = 0; $i < $nbProduct; $i++) {
            $productData = $productArrayData[$i];

            $product = new Product();
            $product->setModel($productData[0])
                ->setDescription($productData[1])
                ->setReleaseDate($productData[2])
                ->setBrand($productData[3])
                ->setType($productData[4])
                ->setPriceHT($productData[5])
                ->setStock($productData[6])
                ->setIsAvailable($productData[7]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
