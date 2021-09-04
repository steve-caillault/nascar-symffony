<?php

/**
 * Formulaire de recherche de pilote
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PilotSearchingType extends AbstractFormType
{

    /**
     * Constructeur
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action = $this->urlGenerator->generate('app_admin_pilots_list_index');

        $builder
            ->setAction($action)
            ->add('searching', options: [
                'label' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('admin.searching.pilots.placeholders.searching', domain: 'form')
                ],
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
