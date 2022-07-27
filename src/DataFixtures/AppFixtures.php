<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        $categories = [];

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName($faker->firstName());

            array_push($categories, $category);
            $manager->persist($category);
        }

        for ($a = 0; $a < 30; $a++) {
            $article = new Article();
            $article->setTitle($faker->sentence())
                ->setContent($faker->text(rand(1000, 1500)))
                ->setCategory($faker->randomElement($categories));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
