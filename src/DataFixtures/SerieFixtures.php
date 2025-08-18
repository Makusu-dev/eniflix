<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SerieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {

            $serie1 = new Serie();
            $serie1->setName($faker->realText(30))
                ->setOverview($faker->paragraph(2))
                ->setGenres($faker->randomElement(['Drama', 'Western', 'Horror', 'Romance', 'Thriller', 'Comedy']))
                ->setStatus($faker->randomElement(['Returning', 'Ending', 'Cancelled']))
                ->setVote($faker->randomFloat(2, 0, 10))
                ->setPopularity($faker->randomFloat(2, 200, 1000))
                ->setFirstAirDate($faker->dateTimeBetween('-10 year', '-1 month'))
                ->setDateCreated(new \DateTime());

            if ($serie1->getStatus() !== 'Returning') {
                $serie1->setLastAirDate($faker->dateTimeBetween($serie1->getFirstAirDate(), '-1 day'));
            }

            $manager->persist($serie1);
        }
        $manager->flush();
    }
}
