<?php

/**
 * Validateur du code d'un pays ou d'un état
 */

namespace App\Validator\Country;

use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator,
    Constraints,
    Validation
};

abstract class StateCodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $constraints = new Constraints\Sequentially([
            new Constraints\NotBlank(message: 'states.edit.code.not_blank'),
            new Constraints\Type('alpha', message: 'states.edit.code.alpha'),
            $this->getCodeLengthConstraint(),
        ]);
 
        $errors = Validation::createValidator()->validate($value->getCode(), $constraints);

        if($errors->count() === 0)
        {
            return;
        }

        $error = $errors->get(0);

        $this->context    
            ->buildViolation($error->getMessage())
            ->atPath('code')
            ->setParameters($error->getParameters())
            ->addViolation();
    }

    /**
     * Retourne la contrainte sur la taille du code
     * @return Constraints\Length
     */
    abstract protected function getCodeLengthConstraint() : Constraints\Length;
    
}
