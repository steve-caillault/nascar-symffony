<?php

/**
 * Entité de gestion des états
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/***/
use App\Repository\CountryStateRepository;

#[
    ORM\Entity(repositoryClass: CountryStateRepository::class),
    ORM\Table(name: 'countries_states'),
    ORM\Index(name: 'fk_country', columns: [ 'country_code' ]),
    /***/
    UniqueEntity(fields: 'code', message: 'countries_states.edit.code.not_exists')
]
final class CountryState extends AbstractStateEntity
{
    /**
     * Code ISO de l'état
     * @var ?string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 2),
    ]
    /**
     * @Constraints\Sequentially({
     *      @Constraints\NotBlank(message="countries_states.edit.code.not_blank"),
     *      @Constraints\Type("alpha", message="countries_states.edit.code.alpha"),
     *      @Constraints\Length(2, exactMessage="countries_states.edit.code.length")
     * })
     */
    protected ?string $code = null;

    /**
     * Nom du pays
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 100),
        Constraints\NotBlank(message: 'countries_states.edit.name.not_blank'),
        Constraints\Length(
            min: 3, 
            max: 100, 
            minMessage: 'countries_states.edit.name.min', 
            maxMessage: 'countries_states.edit.name.max'
        )
    ]
    protected ?string $name = null;

    /**
     * Pays de l'état
     * @var Country
     */
    #[
        ORM\ManyToOne(Country::class),
        ORM\JoinColumn('country_code', referencedColumnName: 'code', nullable: false )
    ]
    private ?string $country = null;

    /**
     * Retourne le pays
     * @return ?Country
     */
    public function getCountry() : ?Country
    {
        return $this->country;
    }

    /**
     * Modifie le pays
     * @param Country $country
     * @return self
     */
    public function setCountry(?Country $country) : self
    {
        $this->country = $country;
        return $this;
    }
}
