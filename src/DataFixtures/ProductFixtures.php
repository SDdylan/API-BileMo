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
            ],
            [
                'Samsung Galaxy S21',
                'Smartphone sous Android 12',
                new \DateTime("2021-01-29"),
                'Samsung',
                $type,
                1059.00,
                1000,
                true
            ],
            [
                'Samsung Galaxy S21 Ultra',
                'Smartphone sous Android 12, version Ultra',
                new \DateTime("2021-01-29"),
                'Samsung',
                $type,
                1259.00,
                500,
                true
            ],
            [
                'Oppo Find X3 Pro',
                'Smartphone sous Android 12, version PRO',
                new \DateTime("2021-03-05"),
                'OPPO',
                $type,
                1149.00,
                500,
                true
            ],
            [
                'Samsung Galaxy Z Fold 3',
                'Smartphone sous Android 12',
                new \DateTime("2021-07-27"),
                'Samsung',
                $type,
                1599.00,
                500,
                true
            ],
            [
                'OnePlus 9',
                'Smartphone sous Android 12',
                new \DateTime("2021-03-23"),
                'OnePlus',
                $type,
                619.00,
                1500,
                true
            ],
            [
                'Google Pixel 6',
                'Smartphone sous Android 12',
                new \DateTime("2021-10-28"),
                'Google',
                $type,
                649.00,
                800,
                true
            ],
            [
                'Google Pixel 6 PRO',
                'Smartphone sous Android 12, version PRO',
                new \DateTime("2021-10-28"),
                'Google',
                $type,
                899.00,
                400,
                true
            ],
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
