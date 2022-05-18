<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Mail\Mail;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_CANDIDATE")]
#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->addFlash("error", "Une erreur est survenue");
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/new/{idCompany}', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository, CompanyRepository $companyRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour gérer les entreprises");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $company = $companyRepository->find($request->get("idCompany"));
            if ($company->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Impossible d'ajouter un contact à cette entreprise");
                return $this->redirectToRoute("app_company_index");
            }
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contact->setCompany($company);
                $contactRepository->add($contact, true);
                $this->addFlash("success", "Le contact a été ajouté à l'entreprise");
                return $this->redirectToRoute('app_company_show', ['id' => $company->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('contact/new.html.twig', [
                'contact' => $contact,
                'form' => $form,
                'company' => $company
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/edit/{id}', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {

        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour gérer les entreprises");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            if ($contact->getCompany()->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Impossible de modifier ce contact");
                return $this->redirectToRoute("app_company_index");
            }
            $form = $this->createForm(ContactType::class, $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contactRepository->add($contact, true);
                $this->addFlash("success", "Le contact de l'entreprise a été modifié avec succès");
                return $this->redirectToRoute('app_company_show', ['id' => $contact->getCompany()->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('contact/edit.html.twig', [
                'contact' => $contact,
                'form' => $form,
                'company' => $contact->getCompany()
            ]);
        }
        return $this->redirectToRoute("app_company_index");
    }

    #[Route('/delete/{id}', name: 'app_contact_delete', methods: ['GET'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
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
            if ($contact->getCompany()->getUser() !== $this->getUser()) {
                $this->addFlash("error", "Impossible de modifier ce contact");
                return $this->redirectToRoute("app_company_index");
            }


            $contactRepository->remove($contact, true);
            $this->addFlash("success", "Le contact de l'entreprise a été correctement supprimée");

            return $this->redirectToRoute('app_company_show', ['id' => $contact->getCompany()->getId()], Response::HTTP_SEE_OTHER);

        }
        return $this->redirectToRoute("app_company_index");

    }
}
