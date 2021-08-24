<?php

/**
 * Gestion du formulaire d'un pays ou d'un état
 */

namespace App\Form\Country;

use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\Country;

final class CountryType extends AbstractStateType
{


    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
