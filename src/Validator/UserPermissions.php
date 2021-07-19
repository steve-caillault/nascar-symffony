<?php

/**
 * Contrainte de vérification des permissions d'un utilisateur
 */

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[
    Attribute(Attribute::TARGET_PROPERTY)
]
final class UserPermissions extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'user.permissions.allowed';
}