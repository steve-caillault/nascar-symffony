<?php

/**
 * Trait pour les classes utilisant Faker
 */

namespace App\Tests;

use Faker\{ 
    Factory as FakerFactory, 
    Generator as FakerGenerator 
};

trait WithFakerTrait {

    /**
     * Objet Faker pour générer de fausse données
     * @var FakerGenerator|false
     */
    private FakerGenerator|false $faker = false;

    /**
     * Retourne l'objet Faker permettant de générer de fausses données
     * @return FakerGenerator
     */
    protected function getFaker() : FakerGenerator
    {
        if($this->faker === false)
        {
            $this->faker = FakerFactory::create();
        }
        return $this->faker;
    }

}