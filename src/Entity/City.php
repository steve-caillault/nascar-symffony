<?php

/**
 * Entité d'une ville
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\CityRepository;

#[
    ORM\Entity(CityRepository::class),
    ORM\Table('cities'),
    ORM\Index(name: 'fk_state', columns: [ 'state' ]),
]
/*final*/ class City
{

    /**
     * Identifiant de la ville
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /**
     * Nom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 50),
        /***/
        Constraints\NotBlank(message: 'cities.edit.name.not_blank'),
        Constraints\Length(min: 4, max: 50, minMessage: 'cities.edit.name.min', maxMessage: 'cities.edit.name.max')
    ]
    private ?string $name = null;

    /**
     * Etat de la ville
     * @var ?CountryState
     */
    #[
        ORM\ManyToOne(CountryState::class),
        ORM\JoinColumn('state', referencedColumnName: 'code', nullable: false, onDelete: 'CASCADE'),
        /***/
        Constraints\NotBlank(message: 'cities.edit.state.not_blank')
    ]
    private ?CountryState $state = null;

    /**
     * Latitude
     * @var float
     */
    #[
        ORM\Column(type: 'float'),
        /***/
        Constraints\NotBlank(message: 'cities.edit.latitude.not_blank'),
        Constraints\Type('float', 'cities.edit.latitude.float')
    ]
    private ?float $latitude = null;

    /**
     * Longitude
     * @var float
     */
    #[
        ORM\Column(type: 'float'),
        /***/
        Constraints\NotBlank(message: 'cities.edit.longitude.not_blank'),
        Constraints\Type('float', 'cities.edit.longitude.float')
    ]
    private ?float $longitude = null;

    /**
     * Retourne l'identifiant de la ville
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
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
     * Retourne l'état de la ville
     * @return ?CountryState
     */
    public function getState() : ?CountryState
    {
        return $this->state;
    }

    /**
     * Modifie l'état
     * @param ?CountryState $state
     * @return self
     */
    public function setState(?CountryState $state) : self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Retourne la latitude
     * @return ?float
     */
    public function getLatitude() : ?float
    {
        return $this->latitude;
    }

    /**
     * Modifie la latitude
     * @param ?float $latitude
     * @return self
     */
    public function setLatitude(?float $latitude) : self
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Retourne la longitude
     * @return ?float
     */
    public function getLongitude() : ?float
    {
        return $this->longitude;
    }

    /**
     * Modifie la longitude
     * @param ?float $longitude
     * @return self
     */
    public function setLongitude(?float $longitude) : self
    {
        $this->longitude = $longitude;
        return $this;
    }
}
