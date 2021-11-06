<?php

/**
 * Entité pour l'historique des identifiants public d'un propriétaire
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OwnerPublicIdHistoryRepository;

#[
    ORM\Entity(OwnerPublicIdHistoryRepository::class),
    ORM\Table('owners_public_ids'),
    ORM\Index(name: 'fk_owner', columns: [ 'owner' ])
]
final class OwnerPublicIdHistory implements EntityInterface, PublicIdHistoryEntityInterface
{
   
    /**
     * Propriétaire
     * @var Owner
     */
    #[
        ORM\Id,
        ORM\ManyToOne(Owner::class),
        ORM\JoinColumn('owner', nullable: false, onDelete: 'CASCADE')
    ]
    private Owner $owner;

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
     * Retourne le propriétaire
     * @return Owner
     */
    public function getOwner() : Owner
    {
        return $this->owner;
    }

    /**
     * Modifie le moteur
     * @param Owner $owner
     * @return self
     */
    public function setOwner(Owner $owner) : self
    {
        $this->owner = $owner;

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
