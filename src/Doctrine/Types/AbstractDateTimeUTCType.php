<?php

/**
 * Gestion de type Datetime UTC pour Doctrine
 */

namespace App\Doctrine\Types;

use DateTime, DateTimeImmutable, DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\{
    ConversionException,
    DateTimeType
};

abstract class AbstractDateTimeUTCType extends DateTimeType
{
    /**
     * Retourne la classe DateTimeInterface à utiliser
     * @return string
     */
    abstract protected function getDateTimeClassName() : string;

    /**
     * Does working with this column require SQL conversion functions?
     *
     * This is a metadata function that is required for example in the ORM.
     * Usage of {@link convertToDatabaseValueSQL} and
     * {@link convertToPHPValueSQL} works for any type and mostly
     * does nothing. This method can additionally be used for optimization purposes.
     *
     * @return bool
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof DateTime or $value instanceof DateTimeImmutable) 
        {
            $value->setTimezone(new DateTimeZone('UTC'));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if($value === null or $value instanceof DateTime or $value instanceof DateTimeImmutable) 
        {
            return $value;
        }

        $dateTimeClassName = $this->getDateTimeClassName();

        $converted = $dateTimeClassName::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            new DateTimeZone('UTC')
        );

        if(! $converted) 
        {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted;
    }
}