<?php

/**
 * Fixtures pour la création initiale des pays
 * bin/console doctrine:fixtures:load --append --group=countries
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
/***/
use App\Entity\Country;

final class CountryFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Retourne les données des pays à créer
     * @return array
     */
    private function getCountriesData() : array
    {
        return [
            [
                'code' => 'AU',
                'name' => 'Australie',
                'image' => 'au.png'
            ],
            [
                'code' => 'CA',
                'name' => 'Canada',
                'image' => 'ca.png',
            ],
            [
                'code' => 'GB',
                'name' => 'Royaume-Uni',
                'image' => 'gb.png',
            ],
            [
                'code' => 'FR',
                'name' => 'France',
                'image' => 'fr.png',
            ],
            [
                'code' => 'MX',
                'name' => 'Mexique',
                'image' => 'mx.png',
            ],
            [
                'code' => 'US',
                'name' => 'États-Unis d\'Amérique',
                'image' => 'us.png',
            ],
        ];
    }

    public function load(ObjectManager $manager)
    {
        $countriesData = $this->getCountriesData();
        foreach($countriesData as $countryData)
        {
            $country = (new Country())
                ->setCode($countryData['code'])
                ->setName($countryData['name'])
                ->setImage($countryData['image'])
            ;

            $manager->persist($country);

            $countryKey = 'COUNTRY_' . $country->getCode();
            $this->addReference($countryKey, $country);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups() : array
    {
        return [ 'countries', 'pilots' ];
    }
}
