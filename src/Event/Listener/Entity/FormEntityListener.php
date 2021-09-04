<?php

/**
 * Gestion des événements de formulaire sur la gestion d'une entité
 */

namespace App\Event\Listener\Entity;

use Symfony\Component\String\UnicodeString;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\{
    FormEvent,
    FormEvents
};
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

final class FormEntityListener implements EventSubscriberInterface {

    /**
     * Entité initiale
     * @var EntityInterface
     */
    private EntityInterface $initialEntity;

    /**
     * Constructeur
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * On stocke dans une variable l'entité initiale avant tous changement
     * @param FormEvent $event
     * @return void
     */
    public function onPreSetData(FormEvent $event) : void
    {
       $this->initialEntity = clone $event->getData();
    }

    /**
     * Dans le cas où une entité n'est pas valide, on rétablit les données initiales pour empêcher une mise à jour
     * si y a un flush postérieur dans l'application
     * @param FormEvent $event
     * @return void
     */
    public function onPostSubmit(FormEvent $event) : void
    {
        $isValid = $event->getForm()->isValid();

        if($isValid)
        {
            return;
        }

        $formEntity = $event->getData();
        $initialEntity = $this->initialEntity;

        // Récupére les propriétés de l'entité pour pouvoir accéder aux getters et setters
        $metadata = $this->entityManager->getClassMetadata(get_class($formEntity));
        $entityFields = $metadata->getFieldNames();
        foreach($entityFields as $entityField)
        {
            $setterMethod = (new UnicodeString('set_' . $entityField))->camel()->toString();
            $getterMethod = (new UnicodeString('get_' . $entityField))->camel()->toString();

            // Rétablissement de la valeur initiale
            if(method_exists($formEntity, $setterMethod) and method_exists($initialEntity, $getterMethod))
            {
                $initialValue = $initialEntity->{ $getterMethod }();
                $formEntity->{ $setterMethod }($initialValue);
            }
        }
    }

}