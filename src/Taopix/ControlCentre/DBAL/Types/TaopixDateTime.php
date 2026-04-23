<?php

namespace Taopix\ControlCentre\DBAL\Types;

use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

class TaopixDateTime extends DateTimeType
{
    /**
     * Converts a mysql datetime to a php value.
     *
     * @param $value
     * @param AbstractPlatform $platform
     * @return \DateTime|DateTimeInterface|mixed|string|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        // If the value when made into a DateTime object has a year less than 0000 set it to the default empty date.
        if ($value instanceof DateTimeInterface && '-0001' === $value->format('Y')) {
            return null;
        }

        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * Converts a php value to a mysql value.
     * null or an invalid datetime will be added as the default empty date.
     *
     * @param $value
     * @param AbstractPlatform $platform
     * @return mixed|string|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value || $value instanceof DateTimeInterface && '-0001' === $value->format('Y')) {
            return '0000-00-00 00:00:00';
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}