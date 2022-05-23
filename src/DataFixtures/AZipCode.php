<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AZipCode extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        ini_set('memory_limit', '1024M');
        dump(ini_get('memory_limit'));
        $zipCode = null;
        $filejson = file_get_contents("https://datanova.laposte.fr/explore/dataset/laposte_hexasmal/download?format=json&amp;timezone=Europe/Berlin&amp;use_labels_for_header=false");
        if ($filejson) {
            $zipCode = json_decode($filejson);
        }

        $sizeArray = count($zipCode);
        $x = 0;

        for ($i = 0; $i < $sizeArray; $i++) {
            $x++;
            if ($x === 200) {
                dump($i + 1);
                $x = 0;
            }
            $city = new City();
            $city->setCity($zipCode[$i]->fields->nom_de_la_commune);
            $city->setZipCode($zipCode[$i]->fields->code_postal);
            $city->setCountry("FRANCE");
            $manager->persist($city);
        }


        $manager->flush();
    }
}
