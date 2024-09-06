<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Flat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {   
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
        
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

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER']);

                $hashPassword = $this->hasher->hashPassword(
                    $user,
                    'password'
                );

                $user->setPassword($hashPassword);


                $manager->persist($user);

        }

        $manager->flush();
    }
}
