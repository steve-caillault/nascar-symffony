<?php

/**
 * Validateur du code d'un pays
 */

namespace App\Validator\Country;

use Symfony\Component\Validator\Constraints;

final class CountryCodeValidator extends StateCodeValidator
{
    /**
     * Retourne la contrainte sur la taille du code
     * @return Constraints\Length
     */
    protected function getCodeLengthConstraint() : Constraints\Length
    {
        return new Constraints\Length(2, exactMessage: 'states.edit.code.length.exactly');
    }
    
}
