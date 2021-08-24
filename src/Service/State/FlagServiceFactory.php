<?php

/**
 * Factory pour la gestion d'un drapeau
 */

namespace App\Service\State;

use App\Entity\AbstractStateEntity as State;

final class FlagServiceFactory {

    /**
     * Constructeur
     * @param CountryFlagService $countryFlagService
     * @param CountryStateFlagService $countryStateFlagService
     */
    public function __construct(
        private CountryFlagService $countryFlagService,
        private CountryStateFlagService $countryStateFlagService
    )
    {

    }

    /**
     * Retourne le service du drapeau en fonction de l'état en paramètre
     * @param State $state
     * @return AbstractFlagService
     */
    public function get(State $state) : AbstractFlagService
    {
        return match($state->getStateType()) {
            State::TYPE_COUNTRY => $this->countryFlagService,
            State::TYPE_STATE => $this->countryStateFlagService, 
            default => throw new \Exception('Unknown service.'),
        };
    }

}