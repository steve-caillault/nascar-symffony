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
use App\Form\AbstractEntityType;

final class CityType extends AbstractEntityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
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
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => City::class,
            'label_format' => 'admin.states.cities.edit.fields.%name%.label',
        ]);
    }
}
