<?php


namespace App\Tests\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

use App\Doctrine\DBAL\Types\IntRangeType;
use App\Doctrine\ValueObject\IntRange;

use PHPUnit\Framework\TestCase;

class IntRangeTypeTest extends TestCase
{
    /** @var  AbstractPlatform */
    protected $platform;

    /** @var IntRangeType */
    protected $type;

    public function testConvertToDatabaseValue(): void
    {
        $range = new IntRange(2, 5);
        $this->assertEquals(
            '[2,5]',
            $this->type->convertToDatabaseValue($range, $this->platform)
        );
        $this->assertNull(
            $this->type->convertToDatabaseValue(null, $this->platform)
        );
    }

    /**
     * @throws ConversionException
     */
    public function testConvertToPHPValueInvalid(): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue('abcd', $this->platform);
    }

    public function testConvertToPHPValue(): void
    {
        $range = $this->type->convertToPHPValue('[2,5]', $this->platform);
        $this->assertEquals(2, $range->getStart());
        $this->assertEquals(5, $range->getEnd());
        $this->assertNull(
            $this->type->convertToPHPValue(null, $this->platform)
        );
    }

    public static function setUpBeforeClass(): void
    {
        Type::addType('IntRange', IntRangeType::class);
    }

    protected function setUp(): void
    {
        $this->platform = new PostgreSqlPlatform();
        $this->type = Type::getType('IntRange');
    }
}