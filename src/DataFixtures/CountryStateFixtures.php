<?php

/**
 * Fixtures pour la création initiale des états
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
use App\Entity\CountryState;

final class CountryStateFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(private Kernel $kernel)
    {

    }

    public function load(ObjectManager $manager)
    {
        $path = $this->kernel->getProjectDir() . '/data/countries_states.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($code, $countryCode, $name, $image) = explode('|', $dataString[0]);

            $countryKey = 'COUNTRY_' . $countryCode;
            $country = $this->getReference($countryKey);
            $countryState = (new CountryState())
                ->setCode($code)
                ->setCountry($country)
                ->setName($name)
                ->setImage($image);
            
            $manager->persist($countryState);

            $countryStateKey = 'COUNTRY_STATE_' . $countryState->getCode();
            $this->addReference($countryStateKey, $countryState);
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
            CountryFixtures::class,
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