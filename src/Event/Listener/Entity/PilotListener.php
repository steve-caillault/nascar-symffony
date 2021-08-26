<?php

/**
 * Gestion des événements Doctrine sur une entité Pilot
 */

namespace App\Event\Listener\Entity;

use Doctrine\ORM\Event\{
    PreUpdateEventArgs,
    LifecycleEventArgs
};
/***/
use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};
use App\Repository\PilotPublicIdHistoryRepository as SlugHistoryRepository;

final class PilotListener {
    
    /**
     * Identifiant public des pilotes à créer
     * Les clés correspondantes aux identifiants des pilotes, les valeur à une entité PilotPublicIdHistory
     * On détermine les identifiants à créer dans l'événement preUpdate
     * La création se fait dans l'événement postUpdate
     */
    private array $oldPublicIdsToCreated = [];

    /**
     * Constructeur
     * @param SlugHistoryRepository $slugHistoryRepository
     */
    public function __construct(private SlugHistoryRepository $slugHistoryRepository)
    {

    }

    /**
     * Détermine l'ancienne version de l'identifiant public du pilote et l'ajoute à celles à ajouter
     * @param Pilot $pilot
     * @param PreUpdateEventArgs $event
     * @return void
     */
    public function preUpdate(Pilot $pilot, PreUpdateEventArgs $event) : void
    {
        $changements = $event->getEntityChangeSet();

        // L'identifiant public n'a pas changé
        $oldValue = $changements['public_id'][0] ?? false;
        if($oldValue === false)
        {
            return;
        }

        // L'identifiant public est déjà présent dans l'historique
        $exists = $this->slugHistoryRepository->exists($pilot, $oldValue);
        if($exists)
        {
            return;
        }

        $this->oldPublicIdsToCreated[$pilot->getId()] = (new PilotPublicIdHistory())->setPilot($pilot)->setPublicId($oldValue);
    }

    /**
     * Ajout de l'ancienne version de l'identifiant public du pilote
     * @param Pilot $pilot
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postUpdate(Pilot $pilot, LifecycleEventArgs $event) : void
    {
        $id = $pilot->getId();
        $historyToAdded = ($this->oldPublicIdsToCreated[$id] ?? null);
        if($historyToAdded === null)
        {
            return;
        }

        unset($this->oldPublicIdsToCreated[$id]);

        $entityManager = $event->getEntityManager();
        $entityManager->persist($historyToAdded);
        $entityManager->flush();
    }


}