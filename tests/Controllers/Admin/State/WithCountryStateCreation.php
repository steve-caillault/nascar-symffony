<?php

/**
 * Trait pour pouvoir créer des états
 */

namespace App\Tests\Controllers\Admin\State;

use App\Entity\{
    Country,
    CountryState
};

trait WithCountryStateCreation {

    /**
     * Création d'un état
     * @param Country $country
     * @param string $code
     * @param string $name
     * @param ?string $image
     * @return CountryState
     */
    protected function createCountryState(
        Country $country, 
        string $code, 
        string $name, 
        ?string $image = null
    ) : CountryState
    {
        $countryState = (new CountryState())
            ->setCountry($country)
            ->setCode($code)
            ->setName($name)
            ->setImage($image)
        ;
        $entityManager = $this->getEntityManager();
        $entityManager->persist($countryState);
        $entityManager->flush();

        return $countryState;
    }

}