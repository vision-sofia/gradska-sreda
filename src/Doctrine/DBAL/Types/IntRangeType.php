<?php


namespace App\Doctrine\DBAL\Types;

use App\Doctrine\ValueObject\IntRange;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

class IntRangeType extends StringType
{
    public const INTRANGE = 'int4range';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return self::INTRANGE;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (null === $value) ? null : IntRange::toString($value);
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {
            if (1 !== preg_match('/^(\[|\()(\d+),(\d+)(\]|\))$/', $value)) {
                throw ConversionException::conversionFailedFormat(
                    $value,
                    $this->getName(),
                    '(\[|\()(\d+),(\d+)(\]|\))$'
                );
            }

            $value = IntRange::fromString($value);
        }

        return $value;
    }

    public function getName(): string
    {
        return self::INTRANGE;
    }
}
