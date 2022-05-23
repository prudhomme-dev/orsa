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

class Entreprises extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '1024M');
        dump(ini_get('memory_limit'));
        $faker = Factory::create('fr_FR');
        $manager->flush();

        for ($i = 1; $i <= 250; $i++) {
            $newCompany = new Company();
            $cities = $manager->getRepository(City::class)->findAll();
            $users = $manager->getRepository(User::class)->findAll();
            $newCompany
                ->setPhoneCompany($faker->phoneNumber())
                ->setCreatedDate($faker->dateTimeBetween('-2 week'))
                ->setCompanyName($faker->company())
                ->setAddress($faker->streetAddress())
                ->setSendCv(0)
                ->setSendCoverletter(0)
                ->setAddressTwo($faker->secondaryAddress())
                ->setEmailCompany($faker->companyEmail())
                ->setUser($users[array_rand($users)])
                ->setCity($cities[array_rand($cities)]);
            $manager->persist($newCompany);
        }

        $manager->flush();

        // Ajout de contacts pour chaque entreprise et statut initial
        $companies = $manager->getRepository(Company::class)->findAll();
        foreach ($companies as $company) {
            $random = random_int(1, 6);
            for ($i = 1; $i <= $random; $i++) {
                $civility = $manager->getRepository(Civility::class)->findAll();
                $contact = new Contact();
                $contact
                    ->setCivility($civility[array_rand($civility)])
                    ->setCompany($company)
                    ->setContactFirstname($faker->firstName)
                    ->setContactLastname($faker->lastName)
                    ->setContactEmail($faker->companyEmail)
                    ->setContactFunction($faker->jobTitle)
                    ->setContactPhone($faker->phoneNumber);
                $manager->persist($contact);
            }
            $manager->flush();
            $applicationNote = new ApplicationNote();
            $applicationNote
                ->setCompany($company)
                ->setDate(DateTimes::getDateTime())
                ->setStatus($manager->getRepository(Status::class)->find(1))
                ->setMessageNote("Création de la fiche de l'entreprise");
            $manager->persist($applicationNote);
            $manager->flush();

            // TODO Création de notes de candidature

        }


    }
}
