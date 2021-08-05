<?php

/**
 * Gestion d'un menu de l'en-tête
 */

namespace App\UI\Menus\Header;

abstract class HeaderMenu
{

	public const 
        TYPE_PRIMARY = 'primary',
        TYPE_SECONDARY = 'secondary',
        TYPE_TERTIARY = 'tertiary'
    ;
	
	/**
	 * Le type de menu
	 * @var string $type
	 */
	protected string $type;
	
	/**
	 * Tableau des éléments du menu
	 * @var array
	 */
	private array $items = [];

	/**
	 * Vrai si le menu a été généré
	 * @var bool
	 */
	private bool $generated = false;
	
	/******************************************************************/

	/**
	 * Ajoute un élément au menu
	 * @param ItemMenu $item Les données de l'objet à ajouter au menu
	 * @return self
	 */
	public function addItem(HeaderItemMenu $item) : self
	{
		$this->items[] = $item;
		return $this;
	}

    /**
     * Ajoute plusieurs éléments au menu
     * @param HeaderItemMenu[] $items
     * @return self
     */
    public function addItems(array $items) : self
    {
        foreach($items as $item)
        {
            $this->addItem($item);
        }
        return $this;
    }
	
	/******************************************************************/

	/**
	 * Génération du menu s'il ne l'a pas été
	 * @return self
	 */
	public function generate() : self
	{
		if($this->generated === false)
		{
			$this->fill();
			$this->generated = true;
		}
		return $this;
	}

	/**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	abstract protected function fill() : void;
	
	/******************************************************************/

	/* GET */

	/**
     * Identifiant du menu
     * @return string
     */
    abstract public function getId() : string;

	/**
	 * Retourne les éléments du menu
	 * @return array
	 */
	public function getItems() : array
	{
		$this->generate();
		return $this->items;
	}

	/**
	 * Retourne le type
	 * @return string
	 */
	public function getType() : string
	{
		return $this->type;
	}

	/******************************************************************/

}