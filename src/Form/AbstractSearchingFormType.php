<?php

/**
 * Formulaire de recherche de base
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractSearchingFormType extends AbstractFormType
{

    /**
     * Constructeur
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator
    )
    {

    }

    /**
     * Retourne l'url de l'action du formulaire
     * @return string
     */
    abstract protected function getAction() : string;

    /**
     * Retourne le texte du label du champs de recherche
     * @return ?string
     */
    abstract protected function getSearchingLabel() : ?string;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action = $this->getAction();

        $builder
            ->setAction($action)
            ->add('searching', options: [
                'label' => $this->getSearchingLabel(),
                'constraints' => [
                    new Constraints\NotBlank(message: 'searching.searching.not_blank'),
                    new Constraints\Length(
                        min: 3, 
                        max: 50, 
                        minMessage: 'searching.searching.min',
                        maxMessage: 'searching.searching.max',
                    )
                ],
            ])
        ;
    }

}
