<?php

/**
 * Formulaire d'une saison
 */

namespace App\Form;


use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    NumberType,
    ChoiceType,
    TextType
};
/***/
use App\Entity\Season;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', NumberType::class, [
                'html5' => true,
                'invalid_message' => 'seasons.edit.year.number',
            ])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'admin.seasons.edit.fields.state.choices.default' => '',
                    'admin.seasons.edit.fields.state.choices.active' => Season::STATE_ACTIVE,
                    'admin.seasons.edit.fields.state.choices.current' => Season::STATE_CURRENT,
                    'admin.seasons.edit.fields.state.choices.disabled' => Season::STATE_DISABLED,
                ],
                'invalid_message' => 'seasons.edit.state.choice',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
            'translation_domain' => 'form',
            'label_format' => 'admin.seasons.edit.fields.%name%.label',
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
