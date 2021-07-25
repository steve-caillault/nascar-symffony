<?php

/**
 * Gestion de type Datetime UTC pour Doctrine
 */

namespace App\Doctrine\Types;

use DateTime;

final class DateTimeUTCType extends AbstractDateTimeUTCType
{
    /**
     * Retourne la classe DateTimeInterface à utiliser
     * @return string
     */
    protected function getDateTimeClassName() : string
    {
        return DateTime::class;
    }
}