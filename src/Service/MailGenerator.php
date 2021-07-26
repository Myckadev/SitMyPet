<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailGenerator
{
    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(MailerInterface $mailer, string $sender, string $receiver, string $subject, string $context = null, $template = null){

        $email = (new Email())
            ->from($sender)
            ->to($receiver)
            ->subject($subject)
            ->text($context)
            ->html($template);

        $mailer->send($email);
    }


}