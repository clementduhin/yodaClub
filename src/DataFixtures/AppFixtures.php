<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Tournois;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create('fr_FR');


        $admin = new User;

        $admin->setEmail('mailAdmin@gmail.com')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->passwordEncoder->encodePassword($admin,'admin'))
               ->setFirstName('Admin')
               ->setLastName('Admin')
               ->setPhone('0936483765')
               ->setPoints(0)
               ->setValidation(false);
        $manager->persist($admin);


        for($i=0; $i<=20; $i++) {
            $user = new User;
            

            $user->setEmail($faker->freeEmail())
                ->setPassword($this->passwordEncoder->encodePassword($user,'coucou'))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->name())
                ->setPhone($faker->phoneNumber())
                ->setPoints( \rand(1, 500))
                ->setValidation(false);
         $manager->persist($user);
        }

        for($i=0; $i<=4; $i++) {
            $tournoi = new Tournois;

            $tournoi->setName('Tournois '.' '.$i)
                    ->setDate(new \DateTime( '2021-'.$i.'-15'))
                    ->setLieu($faker->city())
                    ->setPlaceDispo( \rand(3, 40));
                $manager->persist($tournoi);
        }

        $manager->flush();
    }
}
