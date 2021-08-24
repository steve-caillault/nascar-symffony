<?php

/**
 * Validateur du nom d'un pays ou d'un état
 */

namespace App\Validator\Country;

use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator,
    Constraints,
    Validation
};

final class StateNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $constraints = new Constraints\Sequentially([
            new Constraints\NotBlank(message: 'states.edit.name.not_blank'),
            new Constraints\Length(
                min: 3, 
                max: 100, 
                minMessage: 'states.edit.name.min', 
                maxMessage: 'states.edit.name.max'
            ),
        ]);
 
        $errors = Validation::createValidator()->validate($value->getName(), $constraints);

        if($errors->count() === 0)
        {
            return;
        }

        $error = $errors->get(0);

        $this->context    
            ->buildViolation($error->getMessage())
            ->atPath('name')
            ->setParameters($error->getParameters())
            ->addViolation();
    }
}
