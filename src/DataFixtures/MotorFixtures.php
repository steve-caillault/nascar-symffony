<?php

/**
 * Fixtures pour la création initiale des moteurs
 * bin/console doctrine:fixtures:load --append --group=cars
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\{
    Fixture,
    FixtureGroupInterface
};
use Doctrine\Persistence\ObjectManager;
/***/
use App\Entity\Motor;

final class MotorFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Retourne les données des moteurs à créer
     * @return array
     */
    private function getMotorsData() : array
    {
        return [
            [
                'id' => 1,
                'public_id' => 'chevrolet',
                'name' => 'Chevrolet'
            ],
            [
                'id' => 2,
                'public_id' => 'toyota',
                'name' => 'Toyota',
            ],
            [
                'id' => 3,
                'public_id' => 'ford',
                'name' => 'Ford',
            ],
        ];
    }

    public function load(ObjectManager $manager)
    {
        $motorsData = $this->getMotorsData();
        foreach($motorsData as $motorData)
        {
            $motor = (new Motor())
                ->setPublicId($motorData['public_id'])
                ->setName($motorData['name'])
            ;

            $manager->persist($motor);

            $motorKey = 'MOTOR_' . $motorData['id'];
            $this->addReference($motorKey, $motor);
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
        return [ 'cars', ];
    }
}
