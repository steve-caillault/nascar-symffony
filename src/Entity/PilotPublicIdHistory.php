<?php

/**
 * Entité pour l'historique des identifiants public d'un pilote
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/***/
use App\Repository\PilotPublicIdHistoryRepository;


#[
    ORM\Entity(PilotPublicIdHistoryRepository::class),
    ORM\Table('pilots_public_ids')
]
final class PilotPublicIdHistory implements EntityInterface, PublicIdHistoryEntityInterface
{
    /**
     * Pilote
     * @var Pilot
     */
    #[
        ORM\Id,
        ORM\ManyToOne(Pilot::class),
        ORM\JoinColumn('pilot', nullable: false, onDelete: 'CASCADE')
    ]
    private Pilot $pilot;

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
     * Retourne le pilote
     * @return Pilot
     */
    public function getPilot() : Pilot
    {
        return $this->pilot;
    }

    /**
     * Modifie le pilote
     * @param Pilot $pilot
     * @return self
     */
    public function setPilot(Pilot $pilot) : self
    {
        $this->pilot = $pilot;
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
