<?php

/**
 * Gestion de type Datetime Immutable UTC pour Doctrine
 */

namespace App\Doctrine\Types;

use DateTimeImmutable;

final class DateTimeImmutableUTCType extends AbstractDateTimeUTCType
{
    /**
     * Retourne la classe DateTimeInterface à utiliser
     * @return string
     */
    protected function getDateTimeClassName() : string
    {
        return DateTimeImmutable::class;
    }
}