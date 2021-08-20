<?php

/**
 * Suppression des anciens fichiers téléchargés
 */

namespace App\Event\Listener;

use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
/***/
use App\Service\OldUploadedFilesService;

final class DeleteOldUploadedFilesListener implements EventSubscriberInterface {
    
    /**
     * Constructeur
     * @param OldUploadedFilesService $oldUploadedFilesService
     */
    public function __construct(private OldUploadedFilesService $oldUploadedFilesService)
    {

    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    /**
     * Evénement après une mise à jour
     * @return void
     */
    public function postUpdate(): void
    {
        $this->oldUploadedFilesService->delete();
    }

}