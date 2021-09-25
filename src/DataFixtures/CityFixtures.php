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
    use WithDataFromCSV;

    public function __construct(private Kernel $kernel)
    {
        $this->initDataFromCSV();
    }

    /**
     * Retourne les données à créer
     * @return void
     */
    private function initDataFromCSV() : void
    {
        $path = $this->kernel->getProjectDir() . '/data/cities.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $name, $stateCode, $stateName, $latitude, $longitude) = explode('|', $dataString[0]);

            $this->dataFromCSV[] = [
                'id' => (int) $id,
                'name' => $name,
                'stateCode' => $stateCode, 
                'stateName' => $stateName,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        }

        fclose($file);
    }

    public function load(ObjectManager $manager)
    {
        $dataFromCSV = $this->dataFromCSV;

        foreach($dataFromCSV as $data)
        {
            $stateKey = 'COUNTRY_STATE_' . $data['stateCode'];
            $state = $this->getReference($stateKey);

            $city = (new City())
                ->setName($data['name'])
                ->setState($state)
                ->setLatitude($data['latitude'])
                ->setLongitude($data['longitude']);
            
            $manager->persist($city);

            $cityKey = 'CITY_' . $state->getCode() . '_' . $city->getName();
            $this->addReference($cityKey, $city);
        }

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