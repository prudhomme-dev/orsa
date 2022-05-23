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

class BCivilities extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '1024M');
        dump(ini_get('memory_limit'));
        $faker = Factory::create('fr_FR');
        $manager->flush();

        // Création de Civilités
        $civilitiesArray = ["Monsieur", "Madame", "Docteur", "Maître", "Professeur"];
        foreach ($civilitiesArray as $civilityArray) {
            $civility = new Civility();
            $civility->setNameCivility($civilityArray);
            $manager->persist($civility);
        }
        $manager->flush();

    }
}
