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
            new TwigFunction('anchor', [ $this, 'anchorRender', ], $defaultOptions),
        ];
    }

    /**
     * Retourne une ancre
     * @param string $label Texte de l'ancre
     * @param string $altLabel Texte alternatif
     * @param string $url
     * @return string
     */
    public function anchorRender(string $label, string $altLabel, string $url) : string
    {
        return strtr('<a href=":url" title=":alt">:label</a>', [
            ':label' => $label,
            ':alt' => $altLabel,
            ':url' => $url,
        ]);
    }
}