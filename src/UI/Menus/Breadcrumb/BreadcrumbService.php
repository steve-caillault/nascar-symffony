<?php

/**
 * Service pour le rendu du fil d'ariane
 */

namespace App\UI\Menus\Breadcrumb;

use Twig\Environment as Twig;

final class BreadcrumbService {

    /**
     * Constructeur
     * @param Twig $twig
     */
    public function __construct(private Twig $twig)
    {

    }

    /**
     * Retourne le rendu du fil d'ariane
     * @param Breadcrumb $breadcrumb
     * @return ?string
     */
    public function getRender(Breadcrumb $breadcrumb) : ?string
    {
        $items = $breadcrumb->getItems();
        $countItems = count($items);

        if($countItems < 2)
        {
            return null;
        }

        return $this->twig->render('ui/menus/breadcrumb.html.twig', [
			'items' => $items,
		]);
    }

}