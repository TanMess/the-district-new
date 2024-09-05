<?php

namespace App\DataFixtures;

use App\Entity\Contact;
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
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('demande n°' . ($i +1))
                ->setMessage($this->faker->text());

                $manager->persist($contact);
        }
        $manager->flush();
    }
}
