<?php

/**
 * Formulaire de base
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFormType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        // On définie le domaine de traduction
        // On supprime la validation HTML 5
        $resolver->setDefaults([
            'translation_domain' => 'form',
            'attr' => [
                'autocomplete' => 'off',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
