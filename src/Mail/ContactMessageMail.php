<?php

/**
 * Gestion du mail à envoyer à l'administrateur après la réception d'un message de contact
 */

namespace App\Mail;

use DateTimeZone;
use Symfony\Component\Mime\{
    Email,
    Address
};
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\ContactMessage;

final class ContactMessageMail {

    /**
     * Constructeur
     * @param string $adminEmail
     * @param string $defaultSenderEmail
     * @param string $siteName
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private string $adminEmail, 
        private string $defaultSenderEmail,
        private string $siteName,
        private TranslatorInterface $translator
    )
    {

    }

    /**
     * Retourne l'email à envoyer
     * @param ContactMessage $contactMessage
     * @return Email
     */
    public function getEmail(ContactMessage $contactMessage) : Email
    {
        $email = new Email();

        $email->to($this->adminEmail);
		$fromName = $contactMessage->getFrom();

        // Sujet
        $defaultSubject = $this->translator->trans('contact.subject.default', domain: 'mails');
		$subjet = strtr('[:site_name] :subjet', [
			':subjet' => ($contactMessage->getSubject()) ?: $defaultSubject,
			':site_name' => $this->siteName,
		]);
        $email->subject($subjet);
		
		// Date
        $dateFormatted = $contactMessage
            ->getCreatedAt()
            ->setTimezone(new DateTimeZone('Europe/Paris'))
            ->format('d/m/Y H:i:s')
        ;
        $dateMessage = $this->translator->trans('contact.message.date', [
            'date' => $dateFormatted,
        ], domain: 'mails');
        
		$message = $contactMessage->getMessage() . PHP_EOL . PHP_EOL . $dateMessage;

        // Signature
		if($fromName !== null)
		{
            $signature = $this->translator->trans('contact.message.from', [
                'name' => $fromName,
            ], domain: 'mails');
			$message .= PHP_EOL . PHP_EOL . '---' . PHP_EOL . $signature;
		}

        $email->text($message);

        // Emetteur
		$fromEmail = $contactMessage->getEmail();
		if($fromEmail !== null)
		{
            $fromAddress = new Address($fromEmail, $fromName ?? '');
            $email->replyTo($fromAddress);
		}
        else
        {
            $fromAddress = new Address($this->defaultSenderEmail);
        }

        $email->from($fromAddress);
		
		// En-tête
        $email->getHeaders()->addTextHeader('Content-Type', 'text/plain; charset=utf-8');

        return $email;
    }

}