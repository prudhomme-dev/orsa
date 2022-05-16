<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    #[Route('/city/{zipCode}', name: 'app_json_city')]
    public function ZipCode(Request $request, CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findBy(["zipCode" => $request->get('zipCode')]);
        return $this->json($cities, 200);
    }
}
