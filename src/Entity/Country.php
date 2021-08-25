<?php

/**
 * Entité de gestion d'un pays
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/***/
use App\Repository\CountryRepository;
use App\Validator\Country\CountryCode as CountryCodeValidation;

#[
    ORM\Entity(repositoryClass: CountryRepository::class),
    ORM\Table(name: 'countries'),
    /***/
    UniqueEntity(fields: 'code', message: 'states.edit.code.not_exists.country')
]
/*final*/ class Country extends AbstractStateEntity
{
     /**
     * Retourne le type d'état
     * @return string
     */
    public function getStateType() : string
    {
        return self::TYPE_COUNTRY;
    }

    /**
     * Retourne la validation du code
     * @return CountryCodeValidation
     */
    protected function getCodeValidation() : CountryCodeValidation
    {
        return new CountryCodeValidation();
    }
}
