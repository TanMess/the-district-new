<?php

namespace App\DataFixtures;

use App\Entity\Flat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;
    public function __construct()
    {   
        $this->faker = Factory::create('fr_FR');
        
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {

            $flat = new Flat();
            $flat->setName($this->faker->word());
            $flat->setDescription('riz poulet');
            $flat->setImage('riz poulet');
            $flat->setActive('true')
                ->setPrice(mt_rand(0,100));

            $manager->persist($flat);
        }


        $manager->flush();
    }
}
