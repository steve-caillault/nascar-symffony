<?php

/**
 * Classe appelé par Twig pour la pagination
 * Utilisée pour optimiser Twig à cause de l'injection de dépendance
 * @see https://symfony.com/doc/current/templating/twig_extension.html
 */

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;
/***/
use App\UI\Pagination\{
    Pagination,
    PaginationService
};

final class PaginationRuntime implements RuntimeExtensionInterface
{
    /**
     * Constructeur
     * @param PaginationService $paginationService
     */
    public function __construct(private PaginationService $paginationService)
    {

    }

    /**
     * Retourne le rendu de la pagination
     * @param Pagination $pagination
     * @return ?string
     */
    public function getRender(Pagination $pagination) : ?string
    {
        return $this->paginationService->getRender($pagination);
    }
}