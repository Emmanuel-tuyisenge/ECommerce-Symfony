<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $email;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendsuccessEmail'
        ];
    }


    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        //récupere le user actuellement en ligne(Security)
        /** @var User */
        $currentUser = $this->security->getUser();

        //récupérer la commande(PurchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent->getPurchase();

        //Ecrire le email(templateEmail)
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);

        //envoyer l'email(MailerInterface)
        $this->mailer->send($email);

        $this->logger->info("Emmail envoyé pour la commande n° " .
            $purchaseSuccessEvent->getPurchase()->getId());
    }
}
