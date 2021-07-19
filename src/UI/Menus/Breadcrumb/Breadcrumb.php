<?php

/**
 * Gestion d'un fil d'Ariane
 */

namespace App\UI\Menus\Breadcrumb;

final class Breadcrumb {

	/**
	 * Eléments du fil d'Ariane
	 * @var array
	 */
	private array $items = [];

    /********************************************/
	
	/**
	 * Ajoute un élément au fil d'ariane
	 * @param BreadcrumbItem $item
	 * @return self
	 */
	public function addItem(BreadcrumbItem $item) : self
	{
		$this->items[] = $item;
		return $this;
	}
	
	/**
	 * Retourne les éléments
	 * @return array
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	/********************************************/
	
}

