<?php

/**
 * Validateur du code d'un pays
 */

namespace App\Validator\Country;

use Symfony\Component\Validator\Constraints;

final class CountryStateCodeValidator extends StateCodeValidator
{

    /**
     * Retourne la contrainte sur la taille du code
     * @return Constraints\Length
     */
    protected function getCodeLengthConstraint() : Constraints\Length
    {
        return new Constraints\Length(
            min: 2, 
            max: 3, 
            minMessage: 'states.edit.code.length.min',
            maxMessage: 'states.edit.code.length.max'
        );
    }
    
}
