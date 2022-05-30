<?php

namespace App\Controller;

use App\DateTime\DateTimes;
use App\Entity\Setting;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Mail\Mail;
use App\Security\EmailVerifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute("app_main");
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setActive(true);
            $user->setCreatedDate(DateTimes::getDateTime());
            $user->setRoles(["ROLE_CANDIDATE"]);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $smtp = $entityManager->getRepository(Setting::class)->smtpSettings();
            $mail = new Mail();
            $mail->smtpHost = $smtp["smtp_server"];
            $mail->smtpPort = (int)$smtp["smtp_port"];
            $mail->smtpUser = $smtp["smtp_user"];
            $mail->smtpPwd = $smtp["smtp_pass"];
            $mail->environmentTwig = $this->container->get('twig');
            $mail->smtpFrom = $smtp["smtp_from"];
            $mail->smtpFromName = $smtp["smtp_from_name"];
            $mail->subject = "Bienvenue sur {$_ENV['NAME_SITE']}";
            $mail->template = "registration/confirmation_email.html.twig";
            $mail->context = $this->emailVerifier->getContextLink("app_verify_email", $user);
            $mail->to = ["address" => $user->getEmail(), "name" => "{$user->getFirstnameUser()} {$user->getLastnameUser()}"];
            if ($mail->emailSend()) {
                $this->addFlash("success", "Un E-Mail va vous être envoyé pour activer votre compte");
            } else {
                $this->addFlash("error", "Une erreur système n'a pas permise d'envoyer l'email d'activation de votre compte");
            }

            return $this->render("registration/register-send.html.twig");
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());

        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre E-Mail a bien été vérifiée');

        return $this->redirectToRoute('app_main');
    }
}
