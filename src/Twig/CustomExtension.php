<?php

/**
 * Classe ajoutant des fonctions à Twig
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CustomExtension extends AbstractExtension {

    /**
     * Retourne les fonctions à ajouter à Twig
     * @return array
     */
    public function getFunctions() : array
    {
        $defaultOptions = [
            'is_safe' => [ 'html' ],
        ];

        return [
            new TwigFunction('pagination', [ PaginationRuntime::class, 'getRender', ], $defaultOptions),
        ];
    }


}