<?php

/**
 * Fixtures pour la création initiale des circuits
 * bin/console doctrine:fixtures:load --group=circuits
 */

namespace App\DataFixtures;

use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
/***/
use App\Kernel;
use App\Entity\Circuit;

final class CircuitFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    use WithDataFromCSV;

    /**
     * Constructeur
     * @param Kernel $kernel
     * @param SluggerInterface $slugger
     */
    public function __construct(private Kernel $kernel, private SluggerInterface $slugger)
    {
        $this->initDataFromCSV();
    }

    /**
     * Retourne les données à créer
     * @return void
     */
    private function initDataFromCSV() : void
    {
        $path = $this->kernel->getProjectDir() . '/data/circuits.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $name, $distance, $cityName, $stateCode) = explode('|', $dataString[0]);

            $this->dataFromCSV[] = [
                'id' => (int) $id,
                'name' => $name,
                'distance' => (int) $distance,
                'city' => $cityName, 
                'state' => $stateCode,
            ];
        }

        fclose($file);
    }

    public function load(ObjectManager $manager)
    {
        $dataFromCSV = $this->dataFromCSV;

        foreach($dataFromCSV as $data)
        {
            $circuitName = $data['name'];
            $cityName = $data['city'];
            $circuitSlug = $this->slugger->slug($circuitName);
            $citySlug = $this->slugger->slug($cityName);

            $cityKey = 'CITY_' . $data['state'] . '_' . $citySlug;
            $city = $this->getReference($cityKey);

            $circuit = (new Circuit())
                ->setName($circuitName)
                ->setDistance($data['distance'])
                ->setCity($city);
            
            $manager->persist($circuit);

            $circuitKey = 'CIRCUIT_' . $circuitSlug;
            $this->addReference($circuitKey, $circuit);
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
        return [ 'circuits' ];
    }
}