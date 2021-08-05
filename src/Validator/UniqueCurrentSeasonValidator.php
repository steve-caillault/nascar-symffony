<?php

/**
 * Validation pour qu'il n'est ait qu'une seule saison courante
 */

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
/***/
use App\Entity\Season;
use App\Repository\SeasonRepository;

final class UniqueCurrentSeasonValidator extends ConstraintValidator
{
    /**
     * Constructeur
     * @param SeasonRepository $seasonRepository
     */
    public function __construct(private SeasonRepository $seasonRepository)
    {

    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UniqueCurrentSeason */

        if (null === $value || '' === $value) 
        {
            return;
        }

        // On a besoin d'un objet Season
        $object = $this->context->getObject();
        $expectedObjectClass = Season::class;
        if(! $object instanceof $expectedObjectClass)
        {
            throw new \Exception('Incorrect season type.');
        }

        // Pas de saison courante ou statut diffèrent de CURRENT ou saison courante est celle qui est édité
        $currentSeason = $this->seasonRepository->findCurrent();
        if($currentSeason === null or $value !== Season::STATE_CURRENT or $currentSeason === $object)
        {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ current_year }}', $currentSeason->getYear())
            ->addViolation();
    }
}
