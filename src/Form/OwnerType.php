<?php

/**
 * Edition d'un propriétaire
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\Owner;

final class OwnerType extends AbstractEntityType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('public_id')
            ->add('name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Owner::class,
            'label_format' => 'admin.owners.edit.fields.%name%.label',
        ]);
    }
}
