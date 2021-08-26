<?php

/**
 * Entité de gestion des états
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;
/***/
use App\Repository\CountryStateRepository;
use App\Validator\Country\CountryStateCode as CountryStateCodeValidation;

#[
    ORM\Entity(repositoryClass: CountryStateRepository::class),
    ORM\Table(name: 'countries_states'),
    ORM\Index(name: 'fk_country', columns: [ 'country_code' ]),
    /***/
    UniqueEntity(fields: 'code', message: 'states.edit.code.not_exists.state')
]
/*final*/ class CountryState extends AbstractStateEntity
{

    /**
     * Code ISO
     * @var ?string
     */
    #[
        ORM\Id,
        ORM\Column(type: 'string', length: 3),
    ]
    protected ?string $code = null;

    /**
     * Pays de l'état
     * @var ?Country
     */
    #[
        ORM\ManyToOne(Country::class),
        ORM\JoinColumn('country_code', referencedColumnName: 'code', nullable: false),
        /***/
        Constraints\NotBlank(message: 'states.edit.country.not_blank')
    ]
    private ?Country $country = null;

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

    /**
     * Retourne le type d'état
     * @return string
     */
    public function getStateType() : string
    {
        return self::TYPE_STATE;
    }

    /**
     * Retourne la validation du code
     * @return CountryStateCodeValidation
     */
    protected function getCodeValidation() : CountryStateCodeValidation
    {
        return new CountryStateCodeValidation();
    }

}
