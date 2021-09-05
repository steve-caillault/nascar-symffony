<?php

/**
 * Fixtures pour la création initiale des pilotes
 * bin/console doctrine:fixtures:load --group=pilots
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
use App\Entity\Pilot;

final class PilotFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(private Kernel $kernel)
    {

    }

    public function load(ObjectManager $manager)
    {
        $path = $this->kernel->getProjectDir() . '/data/pilots.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $publicId, $firstName, $lastName, $birthDateStr, $birthCityName, $birthCityStateCode) 
                = explode('|', $dataString[0]);

            $birthCityKey = 'CITY_' . $birthCityStateCode . '_' . $birthCityName;
            $birthCity = $this->getReference($birthCityKey);

            $birthDate = new \DateTimeImmutable($birthDateStr);

            $pilot = (new Pilot())
                ->setPublicId($publicId)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setBirthDate($birthDate)
                ->setBirthCity($birthCity);
            
            $manager->persist($pilot);
;
            $pilotKey = 'PILOT_' . $pilot->getPublicId();
            $this->addReference($pilotKey, $pilot);
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
            CityFixtures::class,
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
        return [ 'pilots' ];
    }
}