<?php

/**
 * Formulaire de recherche de pilote
 */

namespace App\Form;

final class PilotSearchingType extends AbstractSearchingFormType
{

    /**
     * Retourne l'url de l'action du formulaire
     * @return string
     */
    protected function getAction() : string
    {
        return $this->urlGenerator->generate('app_admin_pilots_list_index');
    }

    /**
     * Retourne le texte du label du champs de recherche
     * @return ?string
     */
    protected function getSearchingLabel() : ?string
    {
        return 'admin.searching.pilots.searching.label';
    }

}
