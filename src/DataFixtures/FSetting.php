<?php

namespace App\DataFixtures;

use App\DateTime\DateTimes;
use App\Entity\ApplicationNote;
use App\Entity\City;
use App\Entity\Civility;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Setting;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FSetting extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // SMTP Settings
        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_server")
            ->setValue("localhost")
            ->setLabel("Serveur SMTP");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_port")
            ->setValue("1025")
            ->setLabel("Port du serveur SMTP");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_user")
            ->setLabel("Nom d'utilisateur SMTP");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_pass")
            ->setLabel("Mot de passe SMTP");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_from")
            ->setValue("noreply@monstage.app")
            ->setLabel("Adresse E-Mail d'envoi");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("smtp_from_name")
            ->setValue("MonStage.App")
            ->setLabel("Nom de l'expéditeur d'envoi");
        $manager->persist($settingNew);

        // Status setting
        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("status_initial")
            ->setLabel("Statut des candidatures à la création");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("status_send")
            ->setLabel("Statut des candidatures à l'envoi de la candidature");
        $manager->persist($settingNew);

        $settingNew = new Setting();
        $settingNew
            ->setKeySetting("status_end")
            ->setLabel("Statut des candidatures à l'envoi de la candidature");
        $manager->persist($settingNew);

        $manager->flush();


    }
}
