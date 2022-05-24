<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Civility;
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
        $adminCount = 0;
        for ($i = 1; $i <= 100; $i++) {
            $user = new User();
            if ($adminCount < 7) $roles = ["ROLE_ADMIN", "ROLE_CANDIDATE"];
            else $roles = ["ROLE_CANDIDATE"];
            $civility = $manager->getRepository(Civility::class)->findAll();
            $cities = $manager->getRepository(City::class)->findAll();
            $dateCreated = $faker->dateTimeBetween('-20 week');
            $datelastLogin = $faker->dateTimeBetween($dateCreated, "+8 week");
            $user->setEmail($faker->email)
                ->setRoles([$roles[array_rand($roles)]])
                ->setFirstnameUser($faker->firstName)
                ->setLastnameUser($faker->lastName)
                ->setCreatedDate($dateCreated)
                ->setLastLoginDate($datelastLogin)
                ->setIsVerified(true)
                ->setCivility($civility[array_rand($civility)])
                ->setActive(true)
                ->setAddress($faker->streetAddress())
                ->setAddressTwo($i % 10 === 0 ? $faker->secondaryAddress() : null)
                ->setPhone($faker->phoneNumber())
                ->setMobilePhone($faker->mobileNumber())
                ->setEmailContact($faker->email)
                ->setCity($cities[array_rand($cities)])
                ->setPassword(password_hash($_ENV['PASSWORD_DEFAULT'], PASSWORD_DEFAULT));
            if (array_search("ROLE_ADMIN", $user->getRoles()) === 0) $adminCount++;
            $manager->persist($user);
        }
        $manager->flush();

    }
}
