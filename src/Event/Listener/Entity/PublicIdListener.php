<?php

/**
 * Gestion des événements Doctrine sur une entité gérant un identifiant public
 */

namespace App\Event\Listener\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\{
    PreUpdateEventArgs,
    LifecycleEventArgs
};
/***/
use App\Entity\PublicIdEntityInterface;
use App\Service\PublicIdEntityFactory;

final class PublicIdListener {
    
    /**
     * Identifiant public des entité à créer
     * Les clés correspondantes à class_entité|id_entity, les valeurs à une entité PublicIdHistoryEntityInterface
     * On détermine les identifiants à créer dans l'événement preUpdate
     * La création se fait dans l'événement postUpdate
     */
    private array $oldPublicIdsToCreated = [];

    /**
     * Constructeur
     * @param EntityManagerInterface $entityManager
     * @param PublicIdEntityFactory $publicIdEntityFactory
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PublicIdEntityFactory $publicIdEntityFactory
    )
    {
       
    }
    
    /**
     * Détermine l'ancienne version de l'identifiant public et l'ajoute à celles à créer
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $event) : void
    {
        $entity = $event->getObject();
        if(! $entity instanceof PublicIdEntityInterface)
        {
            return;
        }

        if(! $event->hasChangedField('public_id'))
        {
            return;
        }

        $oldValue = $event->getOldValue('public_id');
    
        $publicIdHistory = $this->publicIdEntityFactory->get($entity)->setPublicId($oldValue);
        $publicIdHistoryClass = get_class($publicIdHistory);
        $slugHistoryRepository = $event->getEntityManager()->getRepository($publicIdHistoryClass);
       
        // L'identifiant existe déjà dans l'historique
        $exists = $slugHistoryRepository->exists($entity, $oldValue);
        if($exists)
        {
            return;
        }

        $entityId = $entity->getId();
        $key = get_class($entity) . '|' . $entityId;
        $this->oldPublicIdsToCreated[$key] = $this->publicIdEntityFactory->get($entity)->setPublicId($oldValue);
    }

    /**
     * Ajout de l'ancienne version de l'identifiant public de l'entité
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $event) : void
    {
        $entity = $event->getObject();
        if(! $entity instanceof PublicIdEntityInterface)
        {
            return;
        }

        $key = get_class($entity) . '|' . $entity->getId();
        $historyToAdded = ($this->oldPublicIdsToCreated[$key] ?? null);
        if($historyToAdded === null)
        {
            return;
        }

        unset($this->oldPublicIdsToCreated[$key]);

        $entityManager = $event->getEntityManager();
        $entityManager->persist($historyToAdded);
        $entityManager->flush();
    }


}