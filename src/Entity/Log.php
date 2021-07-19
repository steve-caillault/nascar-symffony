<?php

/**
 * Gestion de log de message en base de données
 */

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Table(name: 'site_logs'),
    /***/
    ORM\Entity(repositoryClass: LogRepository::class),
    /***/
    ORM\Index(name: 'idx_date', columns: [ 'date' ]),
    ORM\Index(name: 'idx_site_name', columns: [ 'site_name' ]),
    ORM\Index(name: 'idx_uri', columns: [ 'uri' ]),
    ORM\Index(name: 'idx_level', columns: [ 'level' ]),
    ORM\Index(name: 'idx_message', columns: [ 'message' ], options: [ 'lengths' => [ 255 ] ]),
    ORM\Index(name: 'idx_user_agent', columns: [ 'user_agent' ])
]
final class Log implements EntityInterface
{
    /**
     * Identifiant
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'integer', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /**
     * Date
     * @var \DateTimeInterface
     */
    #[
        ORM\Column(type: 'datetime', nullable: false)
    ]
    private \DateTimeInterface $date;

    /**
     * Nom du site
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: false)
    ]
    private string $site_name;

    /**
     * URI
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 255, nullable: true)
    ]
    private ?string $uri = null;

    /**
     * Niveau d'urgence
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 20, nullable: false)
    ]
    private string $level;

    /**
     * Message
     * @var string
     */
    #[
        ORM\Column(type: 'text', length: 10000, nullable: false)
    ]
    private string $message;

    /**
     * User Agent
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 255, nullable: true)
    ]
    private ?string $user_agent = null;

    /**
     * Retourne l'identifiant
     * @return ?int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date
     * @return ?\DateTimeInterface
     */
    public function getDate() : ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Modifie la date
     * @param \DateTimeInterface  $date
     * @return self
     */
    public function setDate(\DateTimeInterface $date) : self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Retourne le nom du site
     * @return ?string
     */
    public function getSiteName() : ?string
    {
        return $this->site_name;
    }

    /**
     * Modifie le nom du site
     * @param string $siteName
     * @return self
     */
    public function setSiteName(string $siteName) : self
    {
        $this->site_name = $siteName;

        return $this;
    }

    /**
     * Retourne l'URI
     * @return ?string
     */
    public function getUri() : ?string
    {
        return $this->uri;
    }

    /**
     * Modifie l'URI
     * @param ?string $uri
     * @return $this
     */
    public function setUri(?string $uri) : self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Retourne le niveau d'urgence
     * @return ?string
     */
    public function getLevel() : ?string
    {
        return $this->level;
    }

    /**
     * Modifie le niveau d'urgence
     * @param string $level
     * @return self
     */
    public function setLevel(string $level) : self
    {
        $this->level = strtoupper($level);
        return $this;
    }

    /**
     * Retourne le message
     * @return ?string
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }

    /**
     * Modifie le message
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message) : self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Retourne l'User Agent
     * @return ?string
     */
    public function getUserAgent() : ?string
    {
        return $this->user_agent;
    }

    /**
     * Modifie l'User Agent
     * @param ?string $userAgent
     * @return self
     */
    public function setUserAgent(?string $userAgent) : self
    {
        $this->user_agent = $userAgent;

        return $this;
    }
}
