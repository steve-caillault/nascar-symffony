<?php

/**
 * Fixtures pour la création initiale des villes
 * bin/console doctrine:fixtures:load --group=countries
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
/***/
use App\Kernel;
use App\Entity\City;

final class CityFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(private Kernel $kernel)
    {

    }

    public function load(ObjectManager $manager)
    {
        $path = $this->kernel->getProjectDir() . '/data/cities.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $name, $countryStateCode, $latitude, $longitude) = explode('|', $dataString[0]);

            $countryStateKey = 'COUNTRY_STATE_' . $countryStateCode;
            $countryState = $this->getReference($countryStateKey);
            
            $city = (new City())
                ->setName($name)
                ->setState($countryState)
                ->setLatitude($latitude)
                ->setLongitude($longitude);
            
            $manager->persist($city);

            $cityKey = 'CITY_' . $countryState->getCode() . '_' . $city->getName();
            $this->addReference($cityKey, $city);
        }
        fclose($file);

        $manager->flush();
    }

    /**
     * Retourne les fixtures à charger avant celle-ci
     * @return array
     */
    public function getDependencies()
    {
        return [
            CountryStateFixtures::class,
        ];
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups() : array
    {
        return [ 'countries', 'pilots', ];
    }
}