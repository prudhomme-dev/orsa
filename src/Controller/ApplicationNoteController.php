<?php

namespace App\Controller;

use App\Entity\ApplicationNote;
use App\Form\ApplicationNoteType;
use App\Repository\ApplicationNoteRepository;
use App\Repository\CompanyRepository;
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
            $form = $this->createForm(ApplicationNoteType::class, $applicationNote);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $applicationNote->setCompany($company);
                $applicationNote->setDate(new DateTime());
                $applicationNoteRepository->add($applicationNote, true);

                return $this->redirectToRoute('app_company_show', ["id" => $company->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('application_note/new.html.twig', [
                'application_note' => $applicationNote,
                'form' => $form,
                'company' => $company
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }
}
