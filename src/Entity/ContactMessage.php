<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Table(name: 'contact_messages'),
    /***/
    ORM\Entity(
        repositoryClass: ContactMessageRepository::class
    )
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
        ORM\Column(type: 'string', length: 100, nullable: true)
    ]
    private ?string $from = null;

    /**
     * Retourne l'adresse email de l'émetteur
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true)
    ]
    private ?string $email = null;

    /**
     * Sujet du message
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true)
    ]
    private ?string $subject = null;

    /**
     * Message
     * @var string
     */
    #[
        ORM\Column(type: 'text', length: 10000)
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
