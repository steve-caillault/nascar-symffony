<?php

/**
 * Fixtures pour la création initiale des propriétaires
 * bin/console doctrine:fixtures:load --append --group=owners
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
/***/
use App\Kernel;
use App\Entity\Owner;

final class OwnerFixtures extends Fixture implements FixtureGroupInterface
{
    use WithDataFromCSV;

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
        $path = $this->kernel->getProjectDir() . '/data/cars_owners.csv';
        $file = @ fopen($path, 'r');

        if($file === false)
        {
            throw new \Exception('Fichier introuvable.');
        }

        while($dataString = fgetcsv($file))
        {
            list($id, $name, $publicId) = explode('|', $dataString[0]);

            $this->dataFromCSV[] = [
                'id' => (int) $id,
                'publicId' => $publicId,
                'name' => $name,
            ];
        }

        fclose($file);
    }

    public function load(ObjectManager $manager)
    {
        $dataFromCSV = $this->dataFromCSV;

        foreach($dataFromCSV as $data)
        {
            $owner = (new Owner())
                ->setName($data['name'])
                ->setPublicId($data['publicId'])
            ;
            
            $manager->persist($owner);

            $ownerKey = 'OWNER_' . $data['id'];

            $this->addReference($ownerKey, $owner);
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
        return [ 'owners', 'cars', ];
    }
}