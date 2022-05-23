<?php

namespace App\Controller;

use App\DateTime\DateTimes;
use App\Entity\ApplicationNote;
use App\Entity\Company;
use App\Entity\Setting;
use App\Form\CompanyType;
use App\Mail\Mail;
use App\Repository\ApplicationNoteRepository;
use App\Repository\CityRepository;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\SettingRepository;
use App\Repository\StatusRepository;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
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
                $company->setCreatedDate(DateTimes::getDateTime());
                $companyRepository->add($company, true);

                $applicationnote = new ApplicationNote();
                $applicationnote->setCompany($company);
                $applicationnote->setStatus($statusRepository->find("1"));
                $applicationnote->setDate(DateTimes::getDateTime());
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

    #[Route('/show/{id}/pdf', name: 'app_company_exportpdf', methods: ['GET'])]
    public function exportPDF(Company $company, ContactRepository $contactRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour exporter votre lettre de motivation en PDF");
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

            if (!$company->getCoverletterContent()) {
                $this->addFlash("error", "Aucun export disponible");
                return $this->redirectToRoute("app_company_show", ["id" => $company->getId()]);
            }
            $this->generatePDF($company->getCoverletterContent(), $company->getCompanyName());
            return new Response('', 200, ['Content-Type' => 'application/pdf']);

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

    #[Route('/application/new/{idCompany}', name: 'app_company_application', methods: ['GET', 'POST'])]
    public function application(Request $request, CompanyRepository $companyRepository, ApplicationNoteRepository $applicationNoteRepository, ContactRepository $contactRepository, StatusRepository $statusRepository, SettingRepository $settingRepository): Response
    {

        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour prospecter une entreprise");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }
        $company = $companyRepository->find($request->get("idCompany"));
        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Entreprise non accessible");
                return $this->redirectToRoute("app_company_index");
            }
        }
        if (count($company->getApplicationNotes()->toArray()) > 1) {
            $this->addFlash("error", "Vous avez déjà prospecté cette entreprise");
            return $this->redirectToRoute("app_company_show", ['id' => $company->getId()]);
        };
        if ($request->getMethod() === "GET") {
            return $this->render('company/application.html.twig', ["company" => $company]);
        }


        $contact = null;
        // Récupération du contact sélectionné
        if ($request->get("contact")) {
            $contact = $contactRepository->find($request->get("contact"));
            // Sécurisation en cas de modification du formulaire
            if ($contact && $contact->getCompany() !== $company) {
                $this->addFlash("error", "Erreur du formulaire : Contact inexistant");
                return $this->render('company/application.html.twig', ["company" => $company]);
            }
        }

        // TODO Rajouter un paramètre dans les statuts - IsDefault & IsSended

        // Récupération du statut
        $statut = $statusRepository->find(2);

        // Modification du contact contenu du CV
        $company->setCoverletterContent($request->get("lettercover"));
        $company = $company->getCoverLetterContentFormat($contact);
        $company->setSendCv($request->get("sendCv") ?: false);
        $company->setSendCoverletter(true);


        // Génération de la note de candidature
        $message = "Candidature envoyée par :";
        switch ($request->get("sendMode")) {
            case "post":
                $message .= " Courrier";
                break;
            case "mail":
                $message .= " E-Mail";
                break;
            case "ext":
                $message .= " Voie Extérieure";
                break;
        }
        if ($contact) {
            $message .= " - Envoyé à {$contact->getCivility()->getNameCivility()} {$contact->getContactFirstname()} {$contact->getContactLastname()}";
        }

        // Création d'une nouvelle note de candidature
        $application = new ApplicationNote();
        $application->setCompany($company);
        $application->setDate(DateTimes::getDateTime());
        $application->setStatus($statut);
        $application->setMessageNote($message);

        // Mise à jour des données dans la base de données
        $applicationNoteRepository->add($application, true);
        $companyRepository->add($company, true);

        switch ($request->get("sendMode")) {
            case "mail":
                $smtp = $settingRepository->smtpSettings();
                $mail = new Mail();
                $mail->smtpHost = $smtp["smtp_server"];
                $mail->smtpPort = (int)$smtp["smtp_port"];
                $mail->smtpUser = $smtp["smtp_user"];
                $mail->smtpPwd = $smtp["smtp_pass"];
                $mail->smtpFromName = "{$this->getUser()->getFirstnameUser()} {$this->getUser()->getLastnameUser()}";
                $mail->smtpFrom = $smtp["smtp_from"];
                $mail->subject = ($request->get("subjectMail"));
                // Ajout du CV en pièce jointe
                if ($company->isSendCv()) $mail->attachments = [$company->getUser()->getUploadedCv()];
                $mail->contentHtml = $company->getCoverletterContent();
                if ($contact && $contact->getContactEmail()) {
                    $mail->to = ["address" => $contact->getContactEmail(), "name" => $contact->getContactFirstname()];
                } else {
                    $mail->to = ["address" => $company->getEmailCompany(), "name" => $company->getCompanyName()];
                }
                $mail->replyto = ["address" => $this->getUser()->getEmailContact(), "name" => "{$this->getUser()->getCivility()->getNameCivility()} {$this->getUser()->getFirstnameUser()} {$this->getUser()->getLastnameUser()}"];
                if ($mail->emailSend()) {
                    $this->addFlash("success", "L'E-Mail a été correctement envoyé");
                } else {
                    $this->addFlash("error", "L'E-Mail n'a pas pu être envoyé, la candidature a été annulée");
                    $applicationNoteRepository->remove($application);
                }
                $this->redirectToRoute("app_company_show", ["id" => $company->getid()]);
                break;
            default:
                $this->generatePDF($company->getCoverletterContent(), $company->getCompanyName());
                return new Response('', 200, ['Content-Type' => 'application/pdf']);

        }
        return $this->redirectToRoute('app_company_show', ["id" =>
            $company->getId()], Response::HTTP_SEE_OTHER);
    }

    private function generatePDF(string $content, string $companyName): Dompdf
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("{$companyName}.pdf", [
            "Attachment" => false
        ]);
        return $dompdf;
    }
}
