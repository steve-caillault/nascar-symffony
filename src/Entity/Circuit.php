<?php

/**
 * Entité représentant un circuit
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\CircuitRepository;


#[
    ORM\Entity(repositoryClass: CircuitRepository::class),
    ORM\Table('circuits'),
    ORM\Index(name: 'fk_city', columns: [ 'city' ]),
]
/* final */ class Circuit implements EntityInterface, AutocompleteEntityInterface
{
    /**
     * Identifiant en base de données
     * @var ?int
     */
    #[
        ORM\Id(),
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /** 
     * Nom du circuit
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'circuits.edit.name.not_blank'),
        Constraints\Length(min: 5, max: 100, minMessage: 'circuits.edit.name.min', maxMessage: 'circuits.edit.name.max')
    ]
    private ?string $name = null;

    /**
     * Ville du circuit
     * @var ?City
     */
    #[
        ORM\ManyToOne(City::class),
        ORM\JoinColumn(name: 'city', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        /***/
        Constraints\NotBlank(message: 'circuits.edit.city.not_blank')
    ]
    private ?City $city = null;

    /**
     * Distance de la piste en mètres
     * @var ?int
     */
    #[
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ]),
        /***/
        Constraints\NotBlank(message: 'circuits.edit.distance.not_blank'),
        Constraints\Type('int', 'circuits.edit.distance.integer'),
        Constraints\Range(
            min: 200, 
            max: 10000, 
            notInRangeMessage: 'circuits.edit.distance.range'
        ),
    ]
    private ?int $distance = null;

    /**
     * Retourne l'identifiant du circuit
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom du circuit
     * @return ?string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Modifie le nom du circuit
     * @param ?string $name
     * @return self
     */
    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retourne la ville
     * @return ?City
     */
    public function getCity() : ?City
    {
        return $this->city;
    }

    /**
     * Modifie la ville
     * @param ?City $city
     * @return self
     */
    public function setCity(?City $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Retourne la distance de la piste en mètre
     * @return ?int
     */
    public function getDistance() : ?int
    {
        return $this->distance;
    }

    /**
     * Modifie la distance de la piste
     * @param ?int $distance
     * @return self
     */
    public function setDistance(?int $distance) : self
    {
        $this->distance = $distance;
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
