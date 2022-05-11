<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Civility;
use App\Entity\Status;
use App\Repository\CityRepository;
use App\Repository\CivilityRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {


        if ($this->getUser()) {
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/init', name: 'app_init')]
    public function initApp(CityRepository $cityRepository, EntityManagerInterface $manager, CivilityRepository $civilityRepository, StatusRepository $statusRepository): Response
    {

        // Ajout des codes postaux
        $cities = $cityRepository->findAll();
        // dd(count($cities));
        if (count($cities) === 0) {
            dd("OK");
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

        return $this->redirectToRoute("app_main");
    }
}