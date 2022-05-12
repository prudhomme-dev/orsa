<?php
declare(strict_types=1);

namespace App\Mail;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Mail
{
    public static ?string $from = null;

    private static function getDsn(array $setting): string
    {
        return "smtp://" . $setting["smtp_user"] . ":" . $setting["smtp_pass"] . "@" . $setting["smtp_server"] . ":" . $setting["smtp_port"];

    }

    public static function emailTransport(array $setting, array $to, string $subject = "", ?string $contenthtml = null, string $template = null, array $context = [], array $replyto = []): bool
    {

        $dsn = self::getDsn($setting);
        $transport = Transport::fromDsn($dsn);
        $customMailer = new Mailer($transport);
        $replyto = $replyto ?: [$to[0], $to[1]];
        if ($template) {
            $email = (new TemplatedEmail())
                ->from(new Address($setting["smtp_from"], $setting["smtp_from_name"]))
                ->to(new Address($to[0], $to[1]))
                ->replyTo(new Address($replyto[0], $replyto[1]))
                ->subject($subject)
                ->htmlTemplate($template)
                ->context($context);
// TODO Trouver solution sur les E-Mail de templates
            $loader = new FilesystemLoader('../templates/');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);
        } else {
            $email = (new Email())
                ->from(new Address($setting["smtp_from"], $setting["smtp_from_name"]))
                ->to(new Address($to[0], $to[1]))
                ->replyTo(new Address($replyto[0], $replyto[1]))
                ->subject($subject)
                ->html($contenthtml);
        }
        try {
            $customMailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}