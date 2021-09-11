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
    /**
     * Données à créer issue d'un fichier CSV
     * @var array
     */
    private array $dataFromCSV = [];

    /**
     * Constructeur
     * @param Kernel $kernel
     */
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
        $path = $this->kernel->getProjectDir() . '/data/pilots.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $publicId, $firstName, $lastName, $birthDate, $birthCity, $birthState) 
                = explode('|', $dataString[0]);

            $fullName = trim(implode(' ', [ $firstName, $lastName, ]));

            $this->dataFromCSV[] = [
                'id' => (int) $id,
                'publicId' => $publicId,
                'firstName' => $firstName,
                'lastName' => $lastName, 
                'fullName' => $fullName,
                'birthDate' => $birthDate,
                'birthCity' => $birthCity,
                'birthState' => $birthState,
            ];
        }

        fclose($file);
    }

    /**
     * Retourne les données du CSV
     * @return array
     */
    public function getDataFromCSV() : array
    {
        return $this->dataFromCSV;
    }

    public function load(ObjectManager $manager)
    {
        $dataFromCSV = $this->dataFromCSV;

        foreach($dataFromCSV as $data)
        {
            $birthCityKey = 'CITY_' . $data['birthState'] . '_' . $data['birthCity'];
            $birthCity = $this->getReference($birthCityKey);

            $birthDate = new \DateTimeImmutable($data['birthDate']);

            $pilot = (new Pilot())
                ->setPublicId($data['publicId'])
                ->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setBirthDate($birthDate)
                ->setBirthCity($birthCity);
            
            $manager->persist($pilot);
;
            $pilotKey = 'PILOT_' . $pilot->getPublicId();
            $this->addReference($pilotKey, $pilot);
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