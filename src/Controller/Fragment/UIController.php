<?php

/**
 * Contrôleur utilisé pour les fragmeents de l'interface
 */

namespace App\Controller\Fragment;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

final class UIController {

    public function header(Request $request) : Response
    {
        dd($request);
    }

}