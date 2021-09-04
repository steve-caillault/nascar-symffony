<?php

/**
 * Formulaire de gestion du entité
 * On attache un écouteur d'événement pour rétablir les données initiales de l'entité en cas d'erreur de validation
 * Un flush pouvant être appelé ailleurs dans l'application cet événement est nécessaire.
 * Plus d'informations à cette adresse : https://github.com/steve-caillault/nascar-symfony/issues/2
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
/***/
use App\Event\Listener\Entity\FormEntityListener;

abstract class AbstractEntityType extends AbstractFormType
{
    /**
     * Gestionnaire d'entité
     * @var EntityManagerInterface 
     */
    private EntityManagerInterface $entityManager;

    /**
     * Modifie le gestionnaire d'entité
     * @param EntityManagerInterface $entityManager
     * @return void
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager) : void
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formEntityListener = (new FormEntityListener($this->entityManager));

        $builder->addEventSubscriber($formEntityListener);
    }

}