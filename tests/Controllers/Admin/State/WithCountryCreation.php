<?php

/**
 * Trait pour pouvoir créer des pays
 */

namespace App\Tests\Controllers\Admin\State;

use App\Entity\Country;

trait WithCountryCreation {

    /**
     * Création d'un pays
     * @param string $code
     * @param string $name
     * @param ?string $image
     * @return Country
     */
    protected function createCountry(string $code, string $name, ?string $image = null) : Country
    {
        $country = (new Country())->setCode($code)->setName($name)->setImage($image);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($country);
        $entityManager->flush();

        return $country;
    }

}