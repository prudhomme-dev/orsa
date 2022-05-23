<?php

namespace App\DataFixtures;

use App\DateTime\DateTimes;
use App\Entity\ApplicationNote;
use App\Entity\City;
use App\Entity\Civility;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DUsers extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $manager->flush();

        // Création d'utilisateur

        for ($i = 1; $i <= 25; $i++) {
            $user = new User();
            $roles = ["ROLE_ADMIN", "ROLE_CANDIDATE"];
            $civility = $manager->getRepository(Civility::class)->findAll();
            $user->setEmail($faker->email)
                ->setRoles([$roles[array_rand($roles)]])
                ->setFirstnameUser($faker->firstName)
                ->setLastnameUser($faker->lastName)
                ->setCreatedDate($faker->dateTimeBetween('-2 week'))
                ->setIsVerified(true)
                ->setCivility($civility[array_rand($civility)])
                ->setActive(true)
                ->setPassword($faker->password(6, 10));
            $manager->persist($user);
        }
        $manager->flush();

    }
}
