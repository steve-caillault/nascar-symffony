<?php

/**
 * Gestion du formulaire d'un état
 */

namespace App\Form\Country;

use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\CountryState;

final class CountryStateType extends AbstractStateType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => CountryState::class,
        ]);
    }
}
