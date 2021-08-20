<?php

/**
 * Entité de gestion d'un pays
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
/***/
use App\Repository\CountryRepository;

#[
    ORM\Entity(repositoryClass: CountryRepository::class),
    ORM\Table(name: 'countries'),
    /***/
    UniqueEntity(fields: 'code', message: 'countries.edit.code.not_exists')
]
final class Country
{
   
    /**
     * Code ISO du pays
     * @var ?string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 2),
    ]
    /**
     * @Constraints\Sequentially({
     *      @Constraints\NotBlank(message="countries.edit.code.not_blank"),
     *      @Constraints\Type("alpha", message="countries.edit.code.alpha"),
     *      @Constraints\Length(2, exactMessage="countries.edit.code.length")
     * })
     */
    private ?string $code = null;

    /**
     * Nom du pays
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        /***/
        Constraints\NotBlank(message: 'countries.edit.name.not_blank'),
        Constraints\Length(min: 3, max: 100, minMessage: 'countries.edit.name.min', maxMessage: 'countries.edit.name.max'),
    ]
    private ?string $name = null;

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
     * @param ?string $code
     * @return self
     */
    public function setCode(?string $code) : self
    {
        $this->code = strtoupper($code);
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
     * @param ?string $name
     * @return self
     */
    public function setName(?string $name) : self
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
     * @param ?string $image
     * @return self
     */
    public function setImage(?string $image) : self
    {
        $this->image = $image;
        return $this;
    }
}
