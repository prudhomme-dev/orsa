<?php

namespace App\Controller;

use App\DateTime\DateTimes;
use App\Entity\City;
use App\Entity\Civility;
use App\Entity\Setting;
use App\Entity\Status;
use App\Entity\User;
use App\Form\ContactUsFormType;
use App\Mail\Mail;
use App\Repository\ApplicationNoteRepository;
use App\Repository\CityRepository;
use App\Repository\CivilityRepository;
use App\Repository\CompanyRepository;
use App\Repository\SettingRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ChartBuilderInterface $chartBuilder, UserRepository $userRepository, ApplicationNoteRepository $applicationNoteRepository, StatusRepository $statusRepository, CompanyRepository $companyRepository, SettingRepository $settingRepository, Request $request): Response
    {
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
        }
        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            // Mise à jour de la date de dernière connexion

            $this->getUser()->setLastLoginDate(DateTimes::getDateTime());
            $userRepository->add($this->getUser(), true);
            if ($this->isGranted('ROLE_CANDIDATE')) {

                // Requêtes des données chiffrées
                $status = $statusRepository->findAll();
                $datas = [];
                $detaildatas = [];
                foreach ($status as $uniqueStatus) {
                    $datas[$uniqueStatus->getStatusName()] = 0;
                    $obj = new stdClass();
                    $obj->id = $uniqueStatus->getId();
                    $obj->statusName = $uniqueStatus->getStatusName();
                    $obj->companies = null;
                    $detaildatas[$uniqueStatus->getId()] = $obj;

                }

                $companies = $companyRepository->findBy(["User" => $this->getUser()]);
                foreach ($companies as $company) {
                    $datas[$company->getApplicationNotes()->last()->getStatus()->getStatusName()]++;
                    $statusId = $company->getApplicationNotes()->last()->getStatus()->getId();
                    if (isset($detaildatas[$statusId])) {
                        $detaildatas[$statusId]->companies[] = $company;
                    }

                }
                // Récupération du statut initial
                $statusInit = $settingRepository->findOneBy(['keySetting' => "status_initial"]);
                return $this->render('main/index_candidate.html.twig', ["datas" => $datas, "kanban" => $detaildatas, "initStatus" => $statusInit->getValue()]);

            }

            if ($this->isGranted('ROLE_ADMIN')) {
                // Requêtes des données chiffrées
                $datas = [];
                // Récupération des comptes inactifs
                $inactiveDay = 30;
                $dateNow = DateTimes::getDateTime();
                $dateSearch = $dateNow->sub(new DateInterval("P{$inactiveDay}D"));
                $inactiveAccount = count($userRepository->createQueryBuilder('u')
                    ->andWhere('u.lastLoginDate < :val')
                    ->setParameter('val', $dateSearch)
                    ->getQuery()
                    ->getResult());
                $datas['inactiveAccount'] = $inactiveAccount;


                // Récupération des nouveaux inscrits
                $dateNow = DateTimes::getDateTime();
                $createdToday = $userRepository->createQueryBuilder('u')
                    ->andWhere('u.createdDate BETWEEN :start AND :end')
                    ->setParameter('start', $dateNow->format("Y-m-d") . " 00:00:00")
                    ->setParameter('end', $dateNow->format("Y-m-d") . " 23:59:59")
                    ->getQuery()
                    ->getResult();
                $datas['createdToday'] = count($createdToday);

                $yesterday = DateTimes::getDateTime()->sub(new DateInterval("P1D"));
                $createdYesterday = $userRepository->createQueryBuilder('u')
                    ->andWhere('u.createdDate BETWEEN :start AND :end')
                    ->setParameter('start', $yesterday->format("Y-m-d") . " 00:00:00")
                    ->setParameter('end', $yesterday->format("Y-m-d") . " 23:59:59")
                    ->getQuery()
                    ->getResult();
                $datas['createdYesterday'] = count($createdYesterday);

                $lastWeek = DateTimes::getPreviousWeek();
                $createdLastWeek = $userRepository->createQueryBuilder('u')
                    ->andWhere('u.createdDate BETWEEN :start AND :end')
                    ->setParameter('start', $lastWeek["start"] . " 00:00:00")
                    ->setParameter('end', $lastWeek["end"] . " 23:59:59")
                    ->getQuery()
                    ->getResult();
                $datas['createdLastWeek'] = count($createdLastWeek);

                $month = 12;
                $arrayMonths = [];
                $arrayDatas = [];
                for ($i = $month; $i >= 0; $i--) {
                    $dateFirstDay = DateTimes::getDateTime();
                    $dateFirstDay->setDate($dateFirstDay->format("Y"), $dateFirstDay->format("m"), 1);
                    $arrayMonths[$dateFirstDay->sub((new DateInterval("P{$i}M")))->format("m/Y")] = 0;
                }

                $status = $statusRepository->findAll();
                foreach ($status as $statusUnique) {
                    for ($i = $month; $i >= 0; $i--) {
                        $dateFirstDay = DateTimes::getDateTime();
                        $dateFirstDay->setDate($dateFirstDay->format("Y"), $dateFirstDay->format("m"), 1);
                        $arrayMonths[$dateFirstDay->sub((new DateInterval("P{$i}M")))->format("m/Y")] = 0;
                    }
                    $dateNow = DateTimes::getDateTime();
                    $dateSearch = $dateNow->sub(new DateInterval("P{$month}M"))->format("Y-m");
                    $countRequest = $applicationNoteRepository->createQueryBuilder('ap')
                        ->andWhere('ap.date BETWEEN :start AND :end AND ap.Status = :status')
                        ->setParameter('start', $dateSearch . "-01 00:00:00")
                        ->setParameter('end', DateTimes::getDateTime()->format("Y-m") . "-31 23:59:59")
                        ->setParameter('status', $statusUnique)
                        ->orderBy("ap.date", "ASC")
                        ->getQuery()
                        ->getResult();
                    foreach ($countRequest as $applicationNote) {
                        if (array_key_exists($applicationNote->getDate()->format("m/Y"), $arrayMonths)) $arrayMonths[$applicationNote->getDate()->format("m/Y")] += 1;
                        else $arrayMonths[$applicationNote->getDate()->format("m/Y")] = 1;
                    }
                    $arrayDatas[$statusUnique->getStatusName()] = $arrayMonths;
                }

                $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
                $datasets = [];
                $colors = 0;
                foreach ($arrayDatas as $key => $data) {

                    $getColors = json_decode($_ENV['COLORS']);
                    $color = $getColors->color[$colors] ?? "rgb(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ")";
                    $datasets[] = [
                        'label' => $key,
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'data' => $data
                    ];
                    $colors++;
                }
                $chart->setData([
                    'labels' => array_keys($arrayMonths),
                    'datasets' => $datasets
                ]);


                $chart->setOptions([
                    'scales' => [
                        'y' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],

                    ],
                    'layout' => ['padding' => 10]
                ]);
                return $this->render('main/index_admin.html.twig', ['datas' => $datas, 'chart' => $chart
                ]);

            }


        }
        return $this->render("main/index.html.twig");


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
        $form = $this->createForm(ContactUsFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $civility = $civilityRepository->find($form->get('Civility')->getData());

            $smtp = $settingRepository->smtpSettings();
            $mail = new Mail();
            $mail->smtpHost = $smtp["smtp_server"];
            $mail->smtpPort = (int)$smtp["smtp_port"];
            $mail->smtpUser = $smtp["smtp_user"];
            $mail->smtpPwd = $smtp["smtp_pass"];
            $mail->environmentTwig = $this->container->get('twig');
            $mail->smtpFromName = $smtp["smtp_from_name"];
            $mail->smtpFrom = $smtp["smtp_from"];
            $mail->subject = "Formulaire de Contact";
            $mail->template = "main/contact_email.html.twig";
            $mail->context = ["form" => $form->all(), "Civility" => $civility];
            $mail->to = ["address" => $smtp["smtp_from"], "name" => $smtp["smtp_from_name"]];
            $mail->replyto = ["address" => $this->getUser() ? $this->getUser()->getEmailContact() : $form->get("Email")->getData(),
                "name" => $this->getUser() ? "{$this->getUser()->getCivility()->getNameCivility()} {$this->getUser()->getFirstnameUser()} {$this->getUser()->getLastnameUser()}" : "{$form->get("Civility")->getData()} {$form->get("LastName")->getData()}"];
            if ($mail->emailSend()) {
                $this->addFlash("mail_send", "Votre message a été envoyé correctement");
                return $this->render("main/contact-send.html.twig");
            } else {
                $this->addFlash("mail_error", "Problème d'envoi du message");
            }

        }

        return $this->render("main/contact.html.twig", ['requestForm' => $form->createView()]);
    }

    #[Route('/init', name: 'app_init')]
    public function initApp(CityRepository $cityRepository, EntityManagerInterface $manager, CivilityRepository $civilityRepository, StatusRepository $statusRepository, SettingRepository $settingRepository, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$userRepository->findAll() || $this->isGranted("ROLE_ADMIN")) {
            ini_set("memory_limit", "2048M");
            ini_set("max_execution_time", "600");


            // Ajout des codes postaux
            $cities = $cityRepository->findAll();
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

            if (count($civilities) === 0) {
                $civilitiesArray = ["Madame", "Docteur", "Maître", "Professeur"];
                foreach ($civilitiesArray as $civilityArray) {
                    $civility = new Civility();
                    $civility->setNameCivility($civilityArray);
                    $manager->persist($civility);
                }
                $manager->flush();
                $this->addFlash("success", "Civilités ajoutées");
            }

            if (!$userRepository->findAll()) {
                // Ajout du premier administrateur
                $user = new User();
                $user->setEmail("admin@monstage.app")
                    ->setRoles(["ROLE_ADMIN"])
                    ->setFirstnameUser("Admin")
                    ->setLastnameUser("Admin")
                    ->setCreatedDate(DateTimes::getDateTime())
                    ->setIsVerified(true)
                    ->setCivility($civilityRepository->find(1))
                    ->setActive(true)
                    ->setPassword($userPasswordHasher->hashPassword(
                        $user,
                        $_ENV["PASSWORD_DEFAULT"]
                    ));
                $userRepository->add($user, true);
                $this->addFlash("success", "Utilisateur admin@monstage.app ajouté avec le mot de passe {$_ENV["PASSWORD_DEFAULT"]}");
            }

            // Ajouts des statuts
            $status = $statusRepository->findAll();

            if (count($status) === 0) {
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
                $this->addFlash("success", "Statuts ajoutées");
            }

            // Ajout des Settings
            $setting = $settingRepository->findAll();
            if (count($setting) === 0) {

                // SMTP Settings
                $settingNew = new Setting();
                $settingNew
                    ->setKeySetting("smtp_server")
                    ->setLabel("Serveur SMTP");
                $manager->persist($settingNew);

                $settingNew = new Setting();
                $settingNew
                    ->setKeySetting("smtp_port")
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
                    ->setLabel("Adresse E-Mail d'envoi");
                $manager->persist($settingNew);

                $settingNew = new Setting();
                $settingNew
                    ->setKeySetting("smtp_from_name")
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
                $this->addFlash("success", "Settings ajoutés");
            }
        }
        return $this->redirectToRoute("app_main");

    }
}