<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;



class MailerController extends AbstractController
{
    #[Route('/mail')]
    public function sendEmail(MailerInterface $mailer): Response
    {  
        $email = (new TemplatedEmail())
            ->from('support@sortir.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Réinitialiser votre mot de passse')
            ->htmlTemplate('/TemplatedEmail/index.html.twig');
        $mailer->send($email);
        return $this->render('/mailer/index.html.twig');
    }
}
