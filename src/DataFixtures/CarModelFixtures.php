<?php

/**
 * Fixtures pour la création initiale des modèles de voiture
 * bin/console doctrine:fixtures:load --append --group=cars
 */

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
/***/
use App\Entity\CarModel;

final class CarModelFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * Retourne les données des modèles de voiture à créer
     * @return array
     */
    public function getCarModelsData() : array
    {
        return [
            [
                'id' => 1,
                'motor' => 1,
                'name' => 'Chevrolet Camaro ZL1',
            ],
            [
                'id' => 2,
                'motor' => 3,
                'name' => 'Ford Fusion',
            ],
            [
                'id' => 3,
                'motor' => 2,
                'name' => 'Toyota Camry',
            ],
            [
                'id' => 4,
                'motor' => 1,
                'name' => 'Chevrolet Camaro ZL1 1LE',
            ],
            [
                'id' => 5,
                'motor' => 3,
                'name' => 'Ford Mustang',
            ],
        ];
    }

    public function load(ObjectManager $manager)
    {
        $carData = $this->getCarModelsData();
        foreach($carData as $carData)
        {
            $motorKey = 'MOTOR_' . $carData['motor'];
            $motor = $this->getReference($motorKey);

            $car = (new CarModel())
                ->setName($carData['name'])
                ->setMotor($motor)
            ;

            $manager->persist($car);

            $carKey = 'CAR_' . $carData['id'];
            $this->addReference($carKey, $car);
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
            MotorFixtures::class,
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
        return [ 'cars' ];
    }
}
