<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Blackbelt',
                'price' => '29.90',
                'image' => 'blackbelt.jpg',
                'featured' => true,
            ],
            [
                'name' => 'BlueBelt',
                'price' => '29.90',
                'image' => 'bluebelt.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Street',
                'price' => '34.50',
                'image' => 'street.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Pokeball',
                'price' => '45.00',
                'image' => 'pokeball.jpg',
                'featured' => true,
            ],
            [
                'name' => 'PinkLady',
                'price' => '29.90',
                'image' => 'pinklady.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Snow',
                'price' => '32.00',
                'image' => 'snow.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Greyback',
                'price' => '28.50',
                'image' => 'greyback.jpg',
                'featured' => false,
            ],
            [
                'name' => 'BlueCloud',
                'price' => '45.00',
                'image' => 'bluecloud.jpg',
                'featured' => false,
            ],
            [
                'name' => 'BornInUsa',
                'price' => '59.90',
                'image' => 'borninusa.jpg',
                'featured' => true,
            ],
            [
                'name' => 'GreenSchool',
                'price' => '42.20',
                'image' => 'greenschool.jpg',
                'featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();

            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            $product->setImage($productData['image']);
            $product->setFeatured($productData['featured']);

            $product->setStockXs(2);
            $product->setStockS(2);
            $product->setStockM(2);
            $product->setStockL(2);
            $product->setStockXl(2);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
