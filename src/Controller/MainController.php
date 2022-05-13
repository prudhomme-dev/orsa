<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Civility;
use App\Entity\Setting;
use App\Entity\Status;
use App\Form\ContactUsFormType;
use App\Mail\Mail;
use App\Repository\CityRepository;
use App\Repository\CivilityRepository;
use App\Repository\SettingRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ChartBuilderInterface $chartBuilder, UserRepository $userRepository): Response
    {
// TODO Revoir la gestion des toasts
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }
        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            // Mise à jour de la date de dernière connexion
            $user = $this->getUser()->setLastLoginDate(new DateTime());
            $userRepository->add($this->getUser(), true);
            if ($this->isGranted('CANDIDATE')) ;
            {


            }

        }

        // Test graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        $this->addFlash('success', 'test');
        // Test graphique
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController', 'chart' => $chart
        ]);
    }

    #[Route('/legal', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render("main/legal.html.twig");
    }

    #[Route('/terms', name: 'app_terms')]
    public function terms(): Response
    {
        // TODO Mettre en place les CGU
        return $this->render("main/terms.html.twig");
    }

    #[Route('/contactus', name: 'app_contactus', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer, TranslatorInterface $translator, CivilityRepository $civilityRepository, SettingRepository $settingRepository): Response
    {
        // TODO Mettre en forme le formulaire sur le Twig avec Bootstrap
        $form = $this->createForm(ContactUsFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $setting = $settingRepository->findBykey("smtp");

            $civility = $civilityRepository->find($form->get('Civility')->getData());
            if (Mail::emailTransport($setting,
                [$setting["smtp_from"], $setting["smtp_from_name"]], "Formulaire de Contact", "", "main/contact_email.html.twig", ["form" => $form->all(), "Civility" => $civility], [$form->get("Email")->getData(), $form->get("LastName")->getData()])) {
                $this->addFlash("mail_send", "Votre message a été envoyé correctement");
                return $this->render("main/contact-send.html.twig");
            } else {
                $this->addFlash("mail_error", "Problème d'envoi du message");
            }


        }

        return $this->render("main/contact.html.twig", ['requestForm' => $form->createView()]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/init', name: 'app_init')]
    public function initApp(CityRepository $cityRepository, EntityManagerInterface $manager, CivilityRepository $civilityRepository, StatusRepository $statusRepository, SettingRepository $settingRepository): Response
    {

        // Ajout des codes postaux
        $cities = $cityRepository->findAll();
        // dd(count($cities));
        if (count($cities) === 0) {
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
            $this->addFlash("success", "Villes ajoutées");

        }

        // Ajout des Civilités
        $civilities = $civilityRepository->findAll();

        if (count($civilities) === 1) {
            $civilitiesArray = ["Madame", "Docteur", "Maître", "Professeur"];
            foreach ($civilitiesArray as $civilityArray) {
                $civility = new Civility();
                $civility->setNameCivility($civilityArray);
                $manager->persist($civility);
            }
            $manager->flush();
            $this->addFlash("success", "Civilités ajoutées");
        }

        // Ajouts des statuts
        $status = $statusRepository->findAll();

        if (count($status) === 0) {
            $statusArray = [
                "Candidatures à réaliser",
                "Candidature envoyée",
                "Candidatures refusée",
                "Candidature à relancer",
                "Entretien planifiée"];
            foreach ($statusArray as $statusOne) {
                $statusInt = new Status();
                $statusInt->setStatusName($statusOne);
                $manager->persist($statusInt);
            }
            $manager->flush();
            $this->addFlash("success", "Statuts ajoutées");
        }

        // Ajout des Settings
        $setting = $settingRepository->findAll();
        if (count($setting) === 0) {
            $settingArray = ["smtp_user", "smtp_pass", "smtp_server", "smtp_port", "smtp_from", "smtp_from_name"];

            foreach ($settingArray as $settingOne) {
                $settingInt = new Setting();
                $settingInt->setKeySetting($settingOne);
                $manager->persist($settingInt);
            }
            $manager->flush();
            $this->addFlash("success", "Settings ajoutés");
        }

        return $this->redirectToRoute("app_main");
    }
}