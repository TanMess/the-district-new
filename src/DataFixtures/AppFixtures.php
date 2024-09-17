<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Flat;
use App\Entity\Mark;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

                $contacts[] = $contact;
                $manager->persist($contact);
        }

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

                $users[] = $user;
                $manager->persist($user);

        }

        $categorys = [];
        for ($i = 0; $i < 25; $i++) {
            $category = new Category();
            $category->setName($this->faker->word());

            $categorys[] = $category;
            $manager->persist($category);
        }


        $flats = [];
        for ($i = 0; $i < 25; $i++) {
            $flat = new Flat();
            $flat->setName($this->faker->word())
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 )
                ->setCategory($categorys[mt_rand(0, count($categorys) - 1)]);

            $flats[] = $flat;
            $manager->persist($flat);
        }


        
        foreach ($flats as $flat) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark;
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setFlat($flat);

                $manager->persist($mark);
            }
        }


        
        $manager->flush();
    }

    
}
