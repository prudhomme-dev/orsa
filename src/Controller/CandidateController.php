<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\CandidateEditUserFormType;
use App\Form\ChangeMailFormType;
use App\Form\ChangePwdFormType;
use App\Mail\Mail;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_CANDIDATE")]
#[Route('/candidate')]
class CandidateController extends AbstractController
{
    private const DIRECTORY_CV = "./upload/";

    private EmailVerifier $emailVerifier;

    #[Route('/profile', name: 'app_candidate_profile')]
    public function profile(Request $request, UserRepository $userRepository, CityRepository $cityRepository): Response
    {

        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour modifier votre profil");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $user = $userRepository->find($this->getUser()->getId());
            $form = $this->createForm(CandidateEditUserFormType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // Upload CV
                $files = $form->get('uploadedCv')->getData();
                if ($files) {
                    $extension = $files->guessExtension();
                    if (!$extension) {
                        // extension cannot be guessed
                        $extension = 'bin';
                    }
                    $newFileName = $user->getUploadedCv() ? explode("/", $user->getUploadedCv())[2] : uniqid("cv_") . "." . $extension;
                    $files->move(self::DIRECTORY_CV, $newFileName);
                    $user->setUploadedCv(self::DIRECTORY_CV . $newFileName);
                }
                $citiSelect = $cityRepository->find($form->get("idCity")->getData());
                $user->setCity($citiSelect);
                $userRepository->add($user, true);
                $this->addFlash("success", "Votre profil candidat a été correctement modifié");
                return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('candidate/profile.html.twig', ['user' => $user,
                'requestForm' => $form, 'city' => $user->getCity() ?: null
            ]);
        }

        return $this->redirectToRoute("app_main");
    }

    #[Route('/edit-pwd', name: 'app_candidate_change_pwd')]
    public function pwd(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour modifier votre mot de passe");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $user = $userRepository->find($this->getUser()->getId());
            $form = $this->createForm(ChangePwdFormType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (!$userPasswordHasher->isPasswordValid($user, $form->get("actualPassword")->getData())) {
                    $this->addFlash("error", "Le mot de passe saisie n'est pas valide");
                    return $this->renderForm('candidate/pwdedit.html.twig', ['user' => $user,
                        'requestForm' => $form, 'city' => $user->getCity()]);
                }

                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get("newPlainPassword")->getData()));
                $userRepository->add($user, true);
                $this->addFlash("success", "Votre mot de passe a été correctement modifié, Veuillez vous reconnecter");

                return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('candidate/pwdedit.html.twig', ['user' => $user,
                'requestForm' => $form]);
        }

        return $this->redirectToRoute("app_main");
    }

    #[Route('/edit-mail', name: 'app_candidate_edit_mail')]
    public function mail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, EmailVerifier $emailVerifier): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour modifier l'adresse E-Mail de votre profil");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $user = $userRepository->find($this->getUser()->getId());
            $form = $this->createForm(ChangeMailFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newEmail = $form->get("newEMail")->getData();
                if ($userRepository->findBy(['email' => $newEmail])) {
                    $this->addFlash("error", "Cette adresse E-Mail ne peut être utilisée.");
                    return $this->renderForm('candidate/mailedit.html.twig', [
                        'requestForm' => $form]);
                }

                if ($user->getUserIdentifier() === $newEmail) {
                    $this->addFlash("error", "La nouvelle adresse est identique à l'ancienne");
                    return $this->renderForm('candidate/mailedit.html.twig', [
                        'requestForm' => $form]);
                }
                $this->emailVerifier = $emailVerifier;
                $user->setEmail($newEmail);
                $user->setIsVerified(false);
                $smtp = $entityManager->getRepository(Setting::class)->smtpSettings();
                $mail = new Mail();
                $mail->smtpHost = $smtp["smtp_server"];
                $mail->smtpPort = (int)$smtp["smtp_port"];
                $mail->smtpUser = $smtp["smtp_user"];
                $mail->smtpPwd = $smtp["smtp_pass"];
                $mail->environmentTwig = $this->container->get('twig');
                $mail->smtpFrom = $smtp["smtp_from"];
                $mail->smtpFromName = $smtp["smtp_from_name"];
                $mail->subject = "{$_ENV['NAME_SITE']} - Validez votre adresse E-Mail";
                $mail->template = "registration/confirmation_email.html.twig";
                $mail->context = $this->emailVerifier->getContextLink("app_verify_email", $user);
                $mail->to = ["address" => $user->getEmail(), "name" => "{$user->getFirstnameUser()} {$user->getLastnameUser()}"];
                if ($mail->emailSend()) {
                    $this->addFlash("success", "Veuillez confirmer votre adresse E-Mail en cliquant sur le lien du mail que vous allez recevoir dans quelques instant");
                    $userRepository->add($user, true);
                } else {
                    $this->addFlash("error", "Une erreur système n'a pas permise d'envoyer l'email d'activation de votre compte");
                }
                // do anything else you need here, like send an email
                return $this->render("registration/register-send.html.twig");

            }
            return $this->renderForm('candidate/mailedit.html.twig', [
                'requestForm' => $form]);
        }
        return $this->redirectToRoute("app_main");
    }

    #[Route('/del-cv', name: 'app_candidate_delcv')]
    public function delcv(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour supprimer le cv de votre profil");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $user = $userRepository->find($this->getUser()->getId());
            $file = $user->getUploadedCv();
            if (!$file) {
                $this->addFlash("error", "Votre profil ne contient pas de CV");
            } else {
                $user->setUploadedCv(null);
                $userRepository->add($user, true);
                @unlink($file);
                $this->addFlash("success", "CV supprimé avec succès");
            }


        }
        return $this->redirectToRoute("app_candidate_profile");
    }

    #[Route('/view-cv', name: 'app_candidate_viewcv')]
    public function viewcv(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            $this->addFlash("error", "Vous devez être connecté pour voir votre CV");
            return $this->redirectToRoute("app_login");
        }
        if ($this->getUser() && !$this->getUser()->isAuthorized()) {
            $this->addFlash("error", "Votre compte est bloqué ou non vérifié");
            return $this->redirectToRoute("app_logout");
        }

        if ($this->getUser() && $this->getUser()->isAuthorized()) {
            $user = $userRepository->find($this->getUser()->getId());
            $file = $user->getUploadedCv();
            if (!$file) {
                $this->addFlash("error", "Votre profil ne contient pas de CV");
                return $this->redirectToRoute("app_candidate_profile");
            }
            return new BinaryFileResponse($user->getUploadedCv());
        }
        return $this->redirectToRoute("app_candidate_profile");
    }
}
