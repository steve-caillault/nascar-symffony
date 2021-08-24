<?php

/**
 * Validation du nom d'un pays ou d'un état
 */

namespace App\Validator\Country;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class StateName extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';
    
}
