<?php

/**
 * Gestion d'une pagination
 */

namespace App\UI\Pagination;

final class Pagination {
	
	public const 
		METHOD_QUERY = 'query',
		METHOD_ROUTE = 'route'
	;
	
	/**
	 * Type de paramètre de la page : dans la route ou en GET
	 * @var string
	 */
	private string $page_parameter_type = self::METHOD_ROUTE;

    /**
     * Nom du paramètre de la page
     * @var string
     */
    private string $page_parameter_name = 'page';
	
	/**
	 * Nombre total d'éléments de la pagination
	 * @return int
	 */
	private int $total_items;
	
	/**
	 * Nombre d'éléments par page
	 * @return int
	 */
	private int $items_per_page;
	
	/**
	 * Nombre de page
	 * @var int
	 */
	private int $total_pages;
	
	/*********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param int $itemsPerPage
     * @param int $totalItems
	 */
	public function __construct(int $itemsPerPage = 20, int $totalItems = 0)
	{
        $this->items_per_page = $itemsPerPage;
        $this->total_items = $totalItems;
        $this->total_pages = ($this->items_per_page === 0 or $this->total_items === 0) ? 0 : ceil($this->total_items / $this->items_per_page);
	}
	
	/*********************************************************************************/

    /**
     * Retourne le nombre d'éléments par page
     * @return int
     */
    public function getItemsPerPage() : int
    {
        return $this->items_per_page;
    }

    /**
     * Retourne le nombre total d'éléments
     * @return int
     */
    public function getTotalItems() : int
    {
        return $this->total_items;
    }

    /**
     * Retourne le nombre total de pages
     * @return int
     */
    public function getTotalPages() : int
    {
        return $this->total_pages;
    }

    /**
     * Retourne le type de paramètre de la page (query ou route)
     * @return string
     */
    public function getPageParameterType() : string
    {
        return $this->page_parameter_type;
    }

    /**
     * Retourne la clé du paramètre de la page
     * @return string
     */
    public function getPageParameterName() : string
    {
        return $this->page_parameter_name;
    }

    /**
     * Modifie le type du paramètre de la page
     * @param string $type
     * @return self
     */
    public function setPageParameterType(string $type) : self
    {
        $allowed = [ self::METHOD_QUERY, self::METHOD_ROUTE, ];
        if(! in_array($type, $allowed))
        {
            throw new \Exception('Type du paramètre de la pagination incorrect.');
        }

        $this->page_parameter_type = $type;

        return $this;
    }
	
    /**
     * Modifie le nom du paramètre de la page
     * @param string $name
     * @return self
     */
    public function setPageParameterName(string $name) : self
    {
        $this->page_parameter_name = $name;
        return $this;
    }

    /*********************************************************************************/

}