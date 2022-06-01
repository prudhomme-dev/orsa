<?php

namespace App\Controller;

use App\DateTime\DateTimes;
use App\Entity\ApplicationNote;
use App\Form\ApplicationNoteFormType;
use App\Repository\ApplicationNoteRepository;
use App\Repository\CompanyRepository;
use App\Repository\StatusRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted("ROLE_CANDIDATE")]
#[Route('/note')]
class ApplicationNoteController extends AbstractController
{
    #[Route('/', name: 'app_application_note_index', methods: ['GET'])]
    public function index(ApplicationNoteRepository $applicationNoteRepository): Response
    {
        $this->addFlash("error", "Erreur système");
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/new/{idCompany}', name: 'app_application_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApplicationNoteRepository $applicationNoteRepository, CompanyRepository $companyRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour gérer les notes de candidatures");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $company = $companyRepository->find($request->get("idCompany"));
            if (!$company) {
                $this->addFlash("error", "L'entreprise n'existe pas");
                return $this->redirectToRoute("app_company_index");
            }
            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Action interdite");
                return $this->redirectToRoute("app_company_index");
            }

            $applicationNote = new ApplicationNote();
            $form = $this->createForm(ApplicationNoteFormType::class, $applicationNote);
            $form->handleRequest($request);

            $lastStatus = $company->getApplicationNotes()->last()->getStatus();

            if ($form->isSubmitted() && $form->isValid()) {
                $applicationNote->setCompany($company);
                $applicationNote->setDate(DateTimes::getDateTime());
                $applicationNoteRepository->add($applicationNote, true);

                return $this->redirectToRoute('app_company_show', ["id" => $company->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('application_note/new.html.twig', [
                'application_note' => $applicationNote,
                'form' => $form,
                'company' => $company,
                'lastStatus' => $lastStatus
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/change/{idCompany}/{idStatus}', name: 'app_note_change_status', methods: ['GET'])]
    public function changeStatus(ApplicationNoteRepository $applicationNoteRepository, CompanyRepository $companyRepository, Request $request, StatusRepository $statusRepository): Response
    {

        $response = [];
        if (!$this->getUser()) {
            $response['success'] = false;
            $response['error'] = "Vous devez être connecté pour gérer les notes de candidatures";
            return $this->json($response, 403);
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $response['success'] = false;
            $response['error'] = "Votre compte est bloqué ou non vérifié";
            return $this->json($response, 403);
        }


        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $company = $companyRepository->find($request->get("idCompany"));
            $status = $statusRepository->find($request->get("idStatus"));
            if (!$company) {
                $response['success'] = false;
                $response['error'] = "L'entreprise n'existe pas";
                return $this->json($response, 403);
            }
            if ($company->getUser() !== $this->getUser()) {
                $response['success'] = false;
                $response['error'] = "Action interdite";
                return $this->json($response, 403);
            }
            if (!$status) {
                $response['success'] = false;
                $response['error'] = "Statut inexistant";
                return $this->json($response, 403);
            }

            $note = new ApplicationNote();
            $note->setCompany($company);
            $note->setStatus($status);
            $note->setDate(DateTimes::getDateTime());
            $note->setMessageNote("Modification du statut de la candidature");
            $applicationNoteRepository->add($note, true);
            $response['success'] = true;
            $this->addFlash("success", "Changement de statut effectué");
            return $this->json($response, 200);

        }

        $response['success'] = false;
        $response['error'] = "Erreur système";
        return $this->json($response, 403);
    }
}
