<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[
    ORM\Table(name: 'contact_messages'),
    /***/
    ORM\Entity(
        repositoryClass: ContactMessageRepository::class
    ),
    ORM\HasLifecycleCallbacks()
]
final class ContactMessage implements EntityInterface
{
    /**
     * Identifiant du message
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true, ])
    ]
    private ?int $id = null;

    /**
     * Retourne le nom de l'émetteur du message
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', name: '`from`', length: 100, nullable: true),
        /***/
        Constraints\Length(
            min: 5,
            max: 100,
            minMessage: 'contact.from.min',
            maxMessage: 'contact.from.max'
        )
    ]
    private ?string $from = null;

    /**
     * Retourne l'adresse email de l'émetteur
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true),
        /***/
        Constraints\Length(
            max: 100,
            maxMessage: 'contact.email.max'
        ),
        Constraints\Email(
            message: 'contact.email.email'
        )
    ]
    private ?string $email = null;

    /**
     * Sujet du message
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true),
        /***/
        Constraints\Length(
            min: 5,
            max: 100,
            minMessage: 'contact.subject.min',
            maxMessage: 'contact.subject.max'
        )
    ]
    private ?string $subject = null;

    /**
     * Message
     * @var string
     */
    #[
        ORM\Column(type: 'text', length: 65535),
        /***/
        Constraints\NotBlank(message: 'contact.message.not_blank'),
        Constraints\Length(
            min: 10,
            max: 65535,
            minMessage: 'contact.message.min',
            maxMessage: 'contact.message.max'
        )
    ]
    private string $message;

    /**
     * Date de création du message
     * @var \DateTimeImmutable
     */
    #[
        ORM\Column(type: 'datetime_immutable')
    ]
    private \DateTimeImmutable $createdAt;

    /**
     * Initialisation de la date de création
     * @return void
     */
    #[
        ORM\PrePersist()
    ]
    public function initCreatedDate() : void
    {
        $timezone = new \DateTimeZone('UTC');
        $createdDate = new \DateTimeImmutable(timezone: $timezone);
        $this->setCreatedAt($createdDate);
    }

    /**
     * Retourne l'identifiant
     * @return ?int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de l'émetteur
     * @return ?string
     */
    public function getFrom() : ?string
    {
        return $this->from;
    }

    /**
     * Modifie le nom de l'émetteur
     * @param string $from
     * @return self
     */
    public function setFrom(string $from) : self
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Retourne l'adresse email de l'émetteur
     * @return ?string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Modifie l'adresse email de l'émetteur
     * @param string $email
     * @return self
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retourne le sujet du message
     * @return ?string
     */
    public function getSubject() : ?string
    {
        return $this->subject;
    }

    /**
     * Modifie le sujet du message
     * @param string $subject
     * @return self
     */
    public function setSubject(string $subject) : self
    {
        $this->subject = $subject;
        return $this;
    }

    /** 
     * Retourne le message
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * Modifie le message
     * @param string $message
     * @return self
     */
    public function setMessage(string $message) : self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Retourne la date de création
     * @return \DateTimeImmutable
     */
    public function getCreatedAt() : \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Modifie la date de création
     * @param \DateTimeImmutable $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}
