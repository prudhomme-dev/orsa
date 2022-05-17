<?php

namespace App\Controller;

use App\Entity\ApplicationNote;
use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\ApplicationNoteRepository;
use App\Repository\CityRepository;
use App\Repository\CompanyRepository;
use App\Repository\StatusRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted("ROLE_CANDIDATE")]
#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour voir les entreprises à prospecter");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }
        $company = $companyRepository->findBy(["User" => $this->getUser()], ["companyName" => "ASC"]);

        return $this->render('company/index.html.twig', [
            'companies' => $company]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CompanyRepository $companyRepository, CityRepository $cityRepository, ApplicationNoteRepository $applicationNoteRepository, StatusRepository $statusRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour ajouter une entreprise");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $company = new Company();
            $form = $this->createForm(CompanyType::class, $company);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $citySelect = $cityRepository->find($form->get("idCity")->getData());
                $company->setCity($citySelect);
                $company->setUser($this->getUser());
                $company->setSendCv(false);
                $company->setSendCoverletter(false);
                $company->setCreatedDate(new DateTime());
                $companyRepository->add($company, true);

                $applicationnote = new ApplicationNote();
                $applicationnote->setCompany($company);
                $applicationnote->setStatus($statusRepository->find("1"));
                $applicationnote->setDate(new DateTime());
                $applicationnote->setMessageNote("Création de la fiche de l'entreprise");
                $applicationNoteRepository->add($applicationnote, true);
                dump($applicationnote);
                $this->addFlash("success", "L'entreprise a été ajoutée avec succès");
                return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('company/new.html.twig', [
                'company' => $company,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/show/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour modifier cette entreprise");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Entreprise non accessible");
                return $this->redirectToRoute("app_company_index");
            }

            $status = $company->getApplicationNotes()->last() ? $company->getApplicationNotes()->last()->getStatus()->getStatusName() : null;
            return $this->render('company/show.html.twig', [
                'company' => $company, 'status' => $status
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/edit/{id}', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, CompanyRepository $companyRepository, CityRepository $cityRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour modifier cette entreprise");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {

            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Entreprise non accessible");
                return $this->redirectToRoute("app_company_index");
            }
            $form = $this->createForm(CompanyType::class, $company);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $citySelect = $cityRepository->find($form->get("idCity")->getData());
                $company->setCity($citySelect);
                $companyRepository->add($company, true);
                return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('company/edit.html.twig', [
                'company' => $company,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/delete/{id}', name: 'app_company_delete', methods: ['GET'])]
    public function delete(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {

        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour supprimer une entreprise");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Entreprise non accessible");
                return $this->redirectToRoute("app_company_index");
            }
        }

        if (count($company->getApplicationNotes()->toArray()) > 1) {
            $this->addFlash("error", "Vous avez déjà prospecté cette entreprise");
            return $this->redirectToRoute("app_company_index");
        }

        $companyRepository->remove($company, true);
        $this->addFlash("success", "L'entreprise a été correctement supprimée");
        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
