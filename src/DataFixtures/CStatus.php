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

class CStatus extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Création des statuts
        $statusArray = [
            "Candidature à réaliser",
            "Candidature envoyée",
            "Candidature refusée",
            "Candidature à relancer",
            "Entretien planifié"];
        foreach ($statusArray as $statusOne) {
            $statusInt = new Status();
            $statusInt->setStatusName($statusOne);
            $manager->persist($statusInt);
        }
        $manager->flush();


    }
}
