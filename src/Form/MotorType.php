<?php

/**
 * Edition d'un moteur
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\Motor;

final class MotorType extends AbstractEntityType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('public_id')
            ->add('name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Motor::class,
            'label_format' => 'admin.motors.edit.fields.%name%.label',
        ]);
    }
}
