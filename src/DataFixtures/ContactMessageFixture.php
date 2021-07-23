<?php

/**
 * Fixture pour les messages de contact
 * bin/console doctrine:fixtures:load --append
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
/***/
use App\Tests\WithFakerTrait;
use App\Entity\ContactMessage;

class ContactMessageFixture extends Fixture
{
    use WithFakerTrait;

    /**
     * 
     */
    public function load(ObjectManager $manager)
    {
        $faker = $this->getFaker();

        for($i = 0 ; $i < 100; $i++)
        {
            $contactMessage = new ContactMessage();

            if($faker->boolean())
            {
                $contactMessage->setFrom($faker->name());
            }
            if($faker->boolean())
            {
                $contactMessage->setEmail($faker->email());
            }
            if($faker->boolean())
            {
                $contactMessage->setSubject($faker->realText(75));
            }

            $createdAt = \DateTimeImmutable::createFromInterface($faker->dateTimeBetween('-2 years', '-1 minute'));
            $contactMessage->setCreatedAt($createdAt);

            $contactMessage->setMessage($faker->realText());

            $manager->persist($contactMessage);
        }

        $manager->flush();
    }
}
