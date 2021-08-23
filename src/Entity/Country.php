<?php

/**
 * Entité de gestion d'un pays
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/***/
use App\Repository\CountryRepository;

#[
    ORM\Entity(repositoryClass: CountryRepository::class),
    ORM\Table(name: 'countries'),
    /***/
    UniqueEntity(fields: 'code', message: 'countries.edit.code.not_exists')
]
final class Country extends AbstractStateEntity
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
    protected ?string $code = null;


    /**
     * Nom du pays
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        Constraints\NotBlank(message: 'countries.edit.name.not_blank'),
        Constraints\Length(min: 3, max: 100, minMessage: 'countries.edit.name.min', maxMessage: 'countries.edit.name.max'),
    ]
    protected ?string $name = null;
}
