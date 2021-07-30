<?php

/**
 * Entité d'une saison
 */

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(SeasonRepository::class),
    ORM\Table('seasons')
]
final class Season
{
    public const 
        STATE_CURRENT = 'CURRENT',
        STATE_DISABLED = 'DISABLED',
        STATE_ACTIVE = 'ACTIVE'
    ;

    /**
     * Identifiant de la saison
     * @var int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private int $id;

    /**
     * Année de la saison
     * @var int
     */
    #[
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private int $year;

    /**
     * Etat de la saison
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 10)
    ]
    private string $state;

    /**
     * Retourne l'identifiant de la saison
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'année de la saison
     * @return int
     */
    public function getYear() : int
    {
        return $this->year;
    }

    /**
     * Retourne l'état de la saison
     * @return string
     */
    public function getState() : string
    {
        return $this->state;
    }
}
