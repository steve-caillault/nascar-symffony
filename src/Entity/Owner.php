<?php

/**
 * Entité pour les propriétaires
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/***/
use App\Repository\OwnerRepository;

#[
    ORM\Entity(repositoryClass: OwnerRepository::class),
    ORM\Table('cars_owners'),
    ORM\UniqueConstraint(name: 'idx_public_id', columns: [ 'public_id' ]),
    /***/
    UniqueEntity('public_id', message: 'owners.edit.public_id.not_exists'),
    UniqueEntity('name', message: 'owners.edit.name.not_exists')
]
class Owner implements EntityInterface, AutocompleteEntityInterface, PublicIdEntityInterface
{
    /**
     * Identifiant en base de données
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
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
        Constraints\NotBlank(message: 'owners.edit.public_id.not_blank'),
        Constraints\Length(
            min: 3,
            max: 100,
            minMessage: 'owners.edit.public_id.min',
            maxMessage: 'owners.edit.public_id.max'
        )
    ]
    private ?string $public_id = null;

    /**
     * Nom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'owners.edit.name.not_blank'),
        Constraints\Length(
            min: 3,
            max: 100,
            minMessage: 'owners.edit.name.min',
            maxMessage: 'owners.edit.name.max'
        )
    ]
    private ?string $name = null;

    /**
     * Retourne l'identifiant en base de données
     * @return ?int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourn l'identifiant public
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
        return $this->id;
    }

}
