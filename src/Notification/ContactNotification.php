<?php


namespace App\Notification;


use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $render;

    public function __construct(\Swift_Mailer $mailer, Environment $render)
    {

        $this->mailer = $mailer;
        $this->render = $render;
    }

    public function notify(Contact $contact){
        $message = new \Swift_Message('Agence : '. $contact->getSubject());
        $message->setFrom('noreply@amj-immo.fr')
                ->setTo('skandernabli34@gmail.com')
                ->setReplyTo($contact->getEmail())
                ->setBody($this->render->render('emails/contact.html.twig',[
                    'contact' => $contact
                ]), 'text/html')
        ;
        $this->mailer->send($message);

    }

}