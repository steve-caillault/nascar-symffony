<?php

/**
 * Entité pour l'historique des identifiants public d'un moteur
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/***/
use App\Repository\MotorPublicIdHistoryRepository;

#[
    ORM\Entity(MotorPublicIdHistoryRepository::class),
    ORM\Table('motors_public_ids'),
    ORM\Index(name: 'fk_motor', columns: [ 'motor' ])
]
final class MotorPublicIdHistory implements EntityInterface, PublicIdHistoryEntityInterface
{
    /**
     * Moteur
     * @var Motor
     */
    #[
        ORM\Id,
        ORM\ManyToOne(Motor::class),
        ORM\JoinColumn('motor', nullable: false, onDelete: 'CASCADE')
    ]
    private Motor $motor;

    /**
     * Identifiant public
     * @var string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 100)
    ]
    private string $public_id;

    /**
     * Retourne le moteur
     * @return Motor
     */
    public function getMotor() : Motor
    {
        return $this->motor;
    }
    
     /**
     * Modifie le moteur
     * @param Motor $motor
     * @return self
     */
    public function setMotor(Motor $motor) : self
    {
        $this->motor = $motor;
        return $this;
    }

    /**
     * Retourne l'identifiant public
     * @return string
     */
    public function getPublicId() : string
    {
        return $this->public_id;
    }

    /**
     * Modifie l'identifiant public
     * @param string $publicId
     * @return self
     */
    public function setPublicId(string $publicId) : self
    {
        $this->public_id = $publicId;
        return $this;
    }

}
