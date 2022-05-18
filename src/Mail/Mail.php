<?php
declare(strict_types=1);

namespace App\Mail;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mail
{
    public static ?string $from = null;
    public ?string $smtpFrom = null;
    public ?string $smtpFromName = null;
    public ?string $smtpHost = null;
    public ?int $smtpPort = null;
    public ?string $smtpUser = null;
    public ?string $smtpPwd = null;
    public ?string $subject = null;
    public ?string $contentHtml = null;
    public ?string $template = null;
    public ?array $context = [];
    public ?array $to = [];
    public ?array $replyto = null;
    public ?string $dns = null;
    public ?Environment $environmentTwig = null;

    private function getDsn(): void
    {
        $this->dns = "smtp://" . $this->smtpUser . ":" . $this->smtpPwd . "@" . $this->smtpHost . ":" . $this->smtpPort;

    }

    public function emailSend(): bool
    {
        $this->getDsn();
        $transport = Transport::fromDsn($this->dns);
        $customMailer = new Mailer($transport);
        $this->replyto = $this->replyto ?: ["address" => $this->to["address"], "name" => $this->to["name"]];
        if ($this->template) {
            $email = (new TemplatedEmail())
                ->from(new Address($this->smtpFrom, $this->smtpFromName))
                ->to(new Address($this->to["address"], $this->to["name"]))
                ->replyTo(new Address($this->replyto["address"], $this->replyto["name"]))
                ->subject($this->subject)
                ->htmlTemplate($this->template)
                ->context($this->context);
            $twigBodyRenderer = new BodyRenderer($this->environmentTwig);
            $twigBodyRenderer->render($email);
        } else {
            $email = (new Email())
                ->from(new Address($this->smtpFrom, $this->smtpFromName))
                ->to(new Address($this->to["address"], $this->to["name"]))
                ->replyTo(new Address($this->replyto["address"], $this->replyto["name"]))
                ->subject($this->subject)
                ->html($this->contentHtml);
        }
        try {
            $customMailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}