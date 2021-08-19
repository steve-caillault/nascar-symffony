<?php

/**
 * Entité de gestion d'un pays
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/***/
use App\Repository\CountryRepository;

#[
    ORM\Entity(repositoryClass: CountryRepository::class),
    ORM\Table(name: 'countries')
]
final class Country
{
   
    /**
     * Code ISO du pays
     * @var string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 2)
    ]
    private string $code;

    /**
     * Nom du pays
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100)
    ]
    private string $name;

    /**
     * Nom du fichier de l'image
     * @var ?string
     */
    #[
        ORM\Column(type: 'string', length: 100, nullable: true)
    ]
    private ?string $image = null;

    /**
     * Retourne le code ISO du pays
     * @return ?string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Modifie le code ISO du pays
     * @param string $code
     * @return self
     */
    public function setCode(string $code) : self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Retourne le nom du pays
     * @return ?string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Modifie le nom du pays
     * @param string $name
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retourne le nom du fichier de l'image
     * @return ?string
     */
    public function getImage() : ?string
    {
        return $this->image;
    }

    /**
     * Modifie le nom du fichier de l'image
     * @param string $image
     * @return self
     */
    public function setImage(string $image) : self
    {
        $this->image = $image;
        return $this;
    }
}
