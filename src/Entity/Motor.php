<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\MotorRepository;

#[
    ORM\Entity(repositoryClass: MotorRepository::class),
    ORM\Table('motors'),
    ORM\UniqueConstraint(name: 'idx_public_id', columns: [ 'public_id' ]),
    /***/
    UniqueEntity('public_id', message: 'motors.edit.public_id.not_exists'),
    UniqueEntity('name', message: 'motors.edit.name.not_exists')
]
final class Motor implements EntityInterface, AutocompleteEntityInterface, PublicIdEntityInterface
{

    /**
     * Identifiant
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /**
     * Identifiant public
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'motors.edit.public_id.not_blank'),
        Constraints\Length(
            min: 5,
            max: 100,
            minMessage: 'motors.edit.public_id.min',
            maxMessage: 'motors.edit.public_id.max'
        )
    ]
    private ?string $public_id = null;

    /**
     * Nom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 20),
        Constraints\NotBlank(message: 'motors.edit.name.not_blank'),
        Constraints\Length(
            min: 4,
            max: 20,
            minMessage: 'motors.edit.name.min',
            maxMessage: 'motors.edit.name.max'
        )
    ]
    private ?string $name = null;

    /**
     * Retourne l'identifiant
     * @return ?int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'identifiant public
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
    public function setPublicId(?string $public_id) : self
    {
        $this->public_id = $public_id;
        return $this;
    }

    /**
     * Retourne le nom
     * @return ?string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Modifie le nom
     * @param ?string $name
     * @return self
     */
    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retourne le texte à afficher dans un champs de formulaire
     * @return string
     */
    public function getAutocompleteDisplayValue() : string
    {
        return $this->getName();
    }

    /**
     * Retourne l'identifiant
     * @return int|string
     */
    public function getAutocompleteId() : int|string
    {
        return $this->getId();
    }
}
