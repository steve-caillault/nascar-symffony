<?php

/**
 * Validation des permissions d'un utilisateur
 */

namespace App\Validator;

use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator
};
use Symfony\Component\Validator\Exception\UnexpectedValueException;
/***/
use App\Entity\User;

final class UserPermissionsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if($value === null or $value === '') 
        {
            return;
        }

        if (! is_array($value)) 
        {
            throw new UnexpectedValueException($value, 'array');
        }

        $user = new User();
        $allowedPermissions = $user->getAllowedPermissions();

        $forbiddenPermissions = array_filter($value, fn($permission) => ! in_array($permission, $allowedPermissions));
        if(count($forbiddenPermissions) === 0)
        {
            return;
        }

        $this->context->buildViolation($constraint->message)
            // ->setTranslationDomain('validators')
            ->atPath('permissions')
            ->addViolation();
    }
}