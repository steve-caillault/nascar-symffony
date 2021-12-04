<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\CarModelRepository;

#[
    ORM\Table('cars_models'),
    ORM\Entity(repositoryClass: CarModelRepository::class),
    ORM\Index(fields: [ 'motor' ], name: 'idx_motor'),
    UniqueEntity(fields: [ 'motor', 'name'], message: 'car_models.edit.name.not_exists', errorPath: 'name')
]
final class CarModel implements EntityInterface
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
     * Moteur
     * @var ?Motor
     */
    #[
        ORM\ManyToOne(targetEntity: Motor::class),
        ORM\JoinColumn(name: 'motor', nullable: false, onDelete: 'CASCADE'),
        /***/
        Constraints\NotBlank(message: 'car_models.edit.motor.not_blank'),
    ]
    private ?Motor $motor = null;

    /**
     * Nom
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 50),
        /***/
        Constraints\NotBlank(message: 'car_models.edit.name.not_blank'),
        Constraints\Length(min: 4, max: 100, minMessage: 'car_models.edit.name.min', maxMessage: 'car_models.edit.name.max'),
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
     * Retourne le moteurs
     * @return ?Motor
     */
    public function getMotor() : ?Motor
    {
        return $this->motor;
    }

    /**
     * Modifie le moteur
     * @param ?Motor $motor
     * @return self
     */
    public function setMotor(?Motor $motor) : self
    {
        $this->motor = $motor;
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
}
