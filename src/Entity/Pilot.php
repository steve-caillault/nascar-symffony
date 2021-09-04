<?php

/**
 * Entité d'un pilote
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\PilotRepository;

#[
    ORM\Entity(PilotRepository::class),
    ORM\Table('pilots'),
    ORM\UniqueConstraint(name: 'idx_public_id', columns: [ 'public_id' ]),
    ORM\Index(name: 'fk_birth_city', fields: [ 'birth_city' ]),
    /***/
    UniqueEntity('public_id', message: 'pilots.edit.public_id.not_exists')
]
class Pilot implements EntityInterface
{
    /**
     * Identifiant
     * @var ?int
     */
    #[
        ORM\Id(),
        ORM\GeneratedValue(),
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /**
     * Identifiant public (slug)
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'pilots.edit.public_id.not_blank'),
        Constraints\Length(
            min:5,
            max: 100,
            minMessage: 'pilots.edit.public_id.min',
            maxmessage: 'pilots.edit.public_id.max'
        )
    ]
    private ?string $public_id = null;

    /**
     * Prénom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'pilots.edit.first_name.not_blank'),
        Constraints\Length(
            min: 2,
            max: 100,
            minMessage: 'pilots.edit.first_name.min',
            maxMessage: 'pilots.edit.first_name.max'
        )
    ]
    private ?string $first_name = null;

    /**
     * Nom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'pilots.edit.last_name.not_blank'),
        Constraints\Length(
            min: 4,
            max: 100,
            minMessage: 'pilots.edit.last_name.min',
            maxMessage: 'pilots.edit.last_name.max'
        )
    ]
    private ?string $last_name = null;

    /**
     * Date de naissance
     * @var ?DateTimeImmutable
     */
    #[
        ORM\Column(type: 'datetime_immutable'),
        /***/
        Constraints\NotBlank(message: 'pilots.edit.birthdate.not_blank'),
        Constraints\Type(\DateTimeInterface::class, message: 'pilots.edit.birthdate.date')
    ]
    private ?\DateTimeImmutable $birthdate = null;

    /**
     * Ville de naissance
     * @var ?City
     */
    #[
        ORM\ManyToOne(City::class),
        ORM\JoinColumn('birth_city', nullable: false, onDelete: 'CASCADE'),
        /***/
        Constraints\NotBlank(message: 'pilots.edit.birth_city.not_blank')
    ]
    private ?City $birth_city = null;

    /**
     * Retourne l'identifiant
     * @return ?int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'identifiant public (slug)
     * @return ?string
     */
    public function getPublicId() : ?string
    {
        return $this->public_id;
    }

    /**
     * Modifie l'identifiant public
     * @param ?string $publicId
     * @return self
     */
    public function setPublicId(?string $publicId) : self
    {
        $this->public_id = $publicId;
        return $this;
    }

    /**
     * Retourne le prénom
     * @return ?string
     */
    public function getFirstName() : ?string
    {
        return $this->first_name;
    }

    /**
     * Modifie le prénom
     * @param ?string $name
     * @return self
     */
    public function setFirstName(?string $name) : self
    {
        $this->first_name = $name;
        return $this;
    }

    /**
     * Retourne le nom
     * @return ?string
     */
    public function getLastName() : ?string
    {
        return $this->last_name;
    }

    /**
     * Modifie le nom
     * @param ?string $name
     * @return self
     */
    public function setLastName(?string $name) : self
    {
        $this->last_name = $name;
        return $this;
    }

    /** 
     * Retourne la date de naissance
     * @return ?\DateTimeImmutable
     */
    public function getBirthDate() : ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    /**
     * Modifie la date de naissance
     * @param ?\DateTimeImmutable $date
     * @return self
     */
    public function setBirthDate(?\DateTimeImmutable $date) : self
    {
        $this->birthdate = $date;
        return $this;
    }

    /**
     * Retourne la ville de naissance
     * @return ?City
     */
    public function getBirthCity() : ?City
    {
        return $this->birth_city;
    }

    /**
     * Modifie la ville de naissance
     * @param ?City $city
     * @return self
     */
    public function setBirthCity(?City $city) : self
    {
        $this->birth_city = $city;
        return $this;
    }

    /**
     * Retourne le nom complet
     * @return string
     */
    public function getFullName() : string
    {
        $values = array_filter([
            $this->getFirstName(), $this->getLastName(),
        ], fn($value) => $value !== null);
        
        return trim(implode(' ', $values));
    }
}
