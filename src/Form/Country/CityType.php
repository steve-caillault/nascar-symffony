<?php

/**
 * Formulaire de gestion d'une ville
 */

namespace App\Form\Country;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\City;

final class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('latitude', options: [
                'invalid_message' => 'cities.edit.latitude.float',
            ])
            ->add('longitude', options: [
                'invalid_message' => 'cities.edit.longitude.float',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'form',
            'data_class' => City::class,
            'label_format' => 'admin.states.cities.edit.fields.%name%.label',
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
            ],
        ]);
    }
}
