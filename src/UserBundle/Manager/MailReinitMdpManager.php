<?php

namespace UserBundle\Manager;

use UserBundle\Entity\User;
use Symfony\Bundle\TwigBundle\TwigEngine;

class MailReinitMdpManager
{

    /** @var TwigEngine */
    private $view;

    /** @var string */
    private $from;

    /** @var \Swift_Mailer */
    private $mailer;

    public function __construct(TwigEngine $view, $from, \Swift_Mailer $mailer)
    {
        $this->view = $view;
        $this->from = $from;
        $this->mailer = $mailer;
    }
    
    /**
     * Permet d'envoyer un e-mail de confirmation que la commande est bien passée.
     *
     * @param User $user
     */
    public function envoyerMailReinitMdp(User $user)
    {
        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')//Message en HTML
            ->setSubject('Réinitialisation de votre mot de passe')
            ->setFrom($this->from)// Email de l'expéditeur est le destinataire du mail
            ->setTo($user->getEmail())// destinataire du mail
            ->setBody($this->view->render('/mail/mailReinit.html.twig', array('user'=>$user))); // contenu du mail

        $this->mailer->send($message);//Envoi mail
    }
}
