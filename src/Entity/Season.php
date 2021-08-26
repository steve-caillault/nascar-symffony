<?php

/**
 * Entité d'une saison
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/***/
use App\Repository\SeasonRepository;
use App\Validator\UniqueCurrentSeason as UniqueCurrentSeasonConstraint;

#[
    ORM\Entity(SeasonRepository::class),
    ORM\Table('seasons'),
    ORM\UniqueConstraint(name: 'idx_year', columns: [ 'year' ]),
    /***/
    UniqueEntity(fields: 'year', message: 'seasons.edit.year.not_exists'),
    /***/
    ORM\HasLifecycleCallbacks()
]
final class Season
{
    public const 
        STATE_CURRENT = 'CURRENT',
        STATE_DISABLED = 'DISABLED',
        STATE_ACTIVE = 'ACTIVE'
    ;

    /**
     * Identifiant de la saison
     * @var ?int
     */
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ])
    ]
    private ?int $id = null;

    /**
     * Année de la saison
     * @var ?int
     */
    #[
        ORM\Column(type: 'smallint', options: [ 'unsigned' => true ]),
        /***/
        Constraints\NotBlank(message: 'seasons.edit.year.not_blank'),
        Constraints\Positive(message: 'seasons.edit.year.number'),
        Constraints\Regex('/^\d{4}$/', message: 'seasons.edit.year.regex')
    ]
    private ?int $year;

    /**
     * Etat de la saison
     * @var string
     */
    #[
        ORM\Column(type: 'string', length: 10),
    ]
    // @todo Surveiller https://github.com/symfony/symfony/issues/38503, pour l'utilisation de Sequentially en attribut PHP 8
    /**
     * @Constraints\Sequentially({
     *      @Constraints\NotBlank(message="seasons.edit.state.not_blank"),
     *      @Constraints\Choice(
                choices={self::STATE_ACTIVE, self::STATE_CURRENT, self::STATE_DISABLED}, 
                message="seasons.edit.state.choice"
            ),
            @UniqueCurrentSeasonConstraint(message="seasons.edit.state.not_exists_current")
     * })
     */
    private string $state;

    /**
     * Retourne l'identifiant de la saison
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'année de la saison
     * @return ?int
     */
    public function getYear() : ?int
    {
        return $this->year;
    }

    /**
     * Retourne l'état de la saison
     * @return string
     */
    public function getState() : string
    {
        return $this->state;
    }

    /**
     * Modifie l'année
     * @param ?int $year
     * @return self
     */
    public function setYear(?int $year) : self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Modifie l'état
     * @param string $state
     * @return self
     */
    public function setState(string $state) : self
    {
        $this->state = $state;
        return $this;
    }
}
