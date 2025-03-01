<?php

declare(strict_types=1);

namespace CBOR\Test\OtherObject;

use CBOR\CBORObject;
use CBOR\Normalizable;
use CBOR\OtherObject\BreakObject;
use CBOR\OtherObject\DoublePrecisionFloatObject;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\HalfPrecisionFloatObject;
use CBOR\OtherObject\NullObject;
use CBOR\OtherObject\SimpleObject;
use CBOR\OtherObject\SinglePrecisionFloatObject;
use CBOR\OtherObject\TrueObject;
use CBOR\OtherObject\UndefinedObject;
use CBOR\StringStream;
use CBOR\Test\CBORTestCase;
use function chr;
use const INF;
use InvalidArgumentException;
use const M_PI;
use const STR_PAD_LEFT;

/**
 * @internal
 */
final class All extends CBORTestCase
{
    /**
     * @test
     */
    public function createValidFalseObject(): void
    {
        $object = FalseObject::create();

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_FALSE, $object->getAdditionalInformation());
        static::assertNull($object->getContent());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame(CBORObject::OBJECT_FALSE, $decoded->getAdditionalInformation());
        static::assertNull($decoded->getContent());
        static::assertFalse($decoded->normalize());
    }

    /**
     * @test
     */
    public function createValidTrueObject(): void
    {
        $object = TrueObject::create();

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_TRUE, $object->getAdditionalInformation());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame(CBORObject::OBJECT_TRUE, $decoded->getAdditionalInformation());
        static::assertNull($decoded->getContent());
        static::assertTrue($decoded->normalize());
    }

    /**
     * @test
     */
    public function createValidNullObject(): void
    {
        $object = NullObject::create();

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_NULL, $object->getAdditionalInformation());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame(CBORObject::OBJECT_NULL, $decoded->getAdditionalInformation());
        static::assertNull($decoded->getContent());
        static::assertNull($decoded->normalize());
    }

    /**
     * @test
     */
    public function createValidUndefinedObject(): void
    {
        $object = UndefinedObject::create();

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_UNDEFINED, $object->getAdditionalInformation());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame(CBORObject::OBJECT_UNDEFINED, $decoded->getAdditionalInformation());
        static::assertNull($decoded->getContent());
        static::assertNotInstanceOf(Normalizable::class, $decoded);
    }

    /**
     * @test
     */
    public function createValidBreakObject(): void
    {
        $object = BreakObject::create();

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_BREAK, $object->getAdditionalInformation());
        static::assertNotInstanceOf(Normalizable::class, $object);
    }

    /**
     * @test
     * @dataProvider getSimpleObjectWithoutContent
     */
    public function createValidSimpleObjectWithoutContent(int $value): void
    {
        $object = SimpleObject::create($value);

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame($value, $object->getAdditionalInformation());
        static::assertNull($object->getContent());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame($value, $decoded->getAdditionalInformation());
        static::assertNull($decoded->getContent());
        //static::assertEquals($value, $decoded->normalize());
    }

    /**
     * @test
     * @dataProvider getHalfPrecisionFloatObject
     */
    public function createValidHalfPrecisionFloatObject(string $value, float $expected, float $delta): void
    {
        $object = HalfPrecisionFloatObject::create($value);

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_HALF_PRECISION_FLOAT, $object->getAdditionalInformation());
        if ($expected === INF || $expected === -INF) {
            static::assertInfinite($object->normalize());
        } else {
            static::assertEqualsWithDelta($expected, $object->normalize(), $delta);
        }
    }

    /**
     * @test
     * @dataProvider getSinglePrecisionFloatObject
     */
    public function createValidSinglePrecisionFloatObject(string $value, float $expected, float $delta): void
    {
        $object = SinglePrecisionFloatObject::create($value);

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_SINGLE_PRECISION_FLOAT, $object->getAdditionalInformation());
        if ($expected === INF || $expected === -INF) {
            static::assertInfinite($object->normalize());
        } else {
            static::assertEqualsWithDelta($expected, $object->normalize(), $delta);
        }
    }

    /**
     * @test
     * @dataProvider getDoublePrecisionFloatObject
     */
    public function createValidDoublePrecisionFloatObject(string $value, float $expected, float $delta): void
    {
        $object = DoublePrecisionFloatObject::create($value);

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_DOUBLE_PRECISION_FLOAT, $object->getAdditionalInformation());
        if ($expected === INF || $expected === -INF) {
            static::assertInfinite($object->normalize());
        } else {
            static::assertEqualsWithDelta($expected, $object->normalize(), $delta);
        }
    }

    /**
     * @test
     * @dataProvider getSimpleObjectWithContent
     */
    public function createValidSimpleObjectWithContent(int $value): void
    {
        $object = SimpleObject::create($value);

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $object->getMajorType());
        static::assertSame(CBORObject::OBJECT_SIMPLE_VALUE, $object->getAdditionalInformation());
        static::assertSame(chr($value), $object->getContent());
        //static::assertEquals($value, $object->normalize());

        $stream = StringStream::create($object->__toString());
        $decoded = $this->getDecoder()
            ->decode($stream)
        ;

        static::assertSame(CBORObject::MAJOR_TYPE_OTHER_TYPE, $decoded->getMajorType());
        static::assertSame(CBORObject::OBJECT_SIMPLE_VALUE, $decoded->getAdditionalInformation());
        static::assertSame(chr($value), $decoded->getContent());
        //static::assertEquals($value, $decoded->normalize());
    }

    /**
     * @test
     */
    public function createInvalidSimpleObjectWithContent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid simple value. Content data should not be present.');

        SimpleObject::createFromLoadedData(0, ' ');
    }

    /**
     * @test
     */
    public function createInvalidSimpleObjectOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value is not a valid simple value.');

        SimpleObject::create(256);
    }

    public function getSimpleObjectWithoutContent(): array
    {
        return [[0], [18], [19]];
    }

    public function getSimpleObjectWithContent(): array
    {
        return [[32], [255]];
    }

    /**
     * @see https://en.wikipedia.org/wiki/Half-precision_floating-point_format
     */
    public function getHalfPrecisionFloatObject(): array
    {
        return [
            [$this->bin('0000000000000001', 2), 0.000000059604645, 0.000000000000001],
            [$this->bin('0000001111111111', 2), 0.000060975552, 0.000000000001],
            [$this->bin('0000010000000000', 2), 0.00006103515625, 0.00000000000001],
            [$this->bin('0111101111111111', 2), 65504, 1],
            [$this->bin('0011101111111111', 2), 0.99951172, 0.00000001],
            [$this->bin('0011110000000000', 2), 1, 1],
            [$this->bin('0011110000000001', 2), 1.00097656, 0.00000001],
            [$this->bin('0011010101010101', 2), 0.333251953125, 0.000000000001],
            [$this->bin('1100000000000000', 2), -1, 1],
            [$this->bin('0000000000000000', 2), 0, 1],
            [$this->bin('1000000000000000', 2), -0, 1],
            [$this->bin('0111110000000000', 2), INF, 1],
            [$this->bin('1111110000000000', 2), -INF, 1],
        ];
    }

    /**
     * @see https://en.wikipedia.org/wiki/Single-precision_floating-point_format
     */
    public function getSinglePrecisionFloatObject(): array
    {
        return [
            [$this->bin('00000000000000000000000000000001', 4), 2 ** -149, 10 ** -149],
            [$this->bin('00000000011111111111111111111111', 4), 1.1754942107 * 10 ** -38, 10 ** -38],
            [$this->bin('00000000100000000000000000000000', 4), 1.1754943508 * 10 ** -38, 10 ** -38],
            [$this->bin('01111111011111111111111111111111', 4), 3.4028234664 * 10 ** 38, 10 ** 38],
            [$this->bin('00111111011111111111111111111111', 4), 0.999999940395355225, 0.000000000000000001],
            [$this->bin('00111111100000000000000000000000', 4), 1, 1],
            [$this->bin('00111111100000000000000000000001', 4), 1.00000011920928955, 0.00000000000000001],
            [$this->bin('11000000000000000000000000000000', 4), -2, 1],
            [$this->bin('00000000000000000000000000000000', 4), 0, 0],
            [$this->bin('10000000000000000000000000000000', 4), -0, 0],
            [$this->bin('01111111100000000000000000000000', 4), INF, 0],
            [$this->bin('11111111100000000000000000000000', 4), -INF, 0],
            [$this->bin('01000000010010010000111111011011', 4), 3.14159274101257324, 0.00000000000000001],
            [$this->bin('00111110101010101010101010101011', 4), 0.333333343267440796, 0.000000000000000001],
        ];
    }

    /**
     * @see https://en.wikipedia.org/wiki/Double-precision_floating-point_format
     */
    public function getDoublePrecisionFloatObject(): array
    {
        return [
            [$this->bin('0011111111110000000000000000000000000000000000000000000000000000', 8), 1, 1],
            [
                $this->bin('0011111111110000000000000000000000000000000000000000000000000001', 8),
                1.0000000000000002,
                0.0000000000000001,
            ],
            [
                $this->bin('0011111111110000000000000000000000000000000000000000000000000010', 8),
                1.0000000000000004,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000000000000000000000000000000000000000000000000000000000', 8),
                2,
                0.0000000000000001,
            ],
            [
                $this->bin('1100000000000000000000000000000000000000000000000000000000000000', 8),
                -2,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000001000000000000000000000000000000000000000000000000000', 8),
                3,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000010000000000000000000000000000000000000000000000000000', 8),
                4,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000010100000000000000000000000000000000000000000000000000', 8),
                5,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000011000000000000000000000000000000000000000000000000000', 8),
                6,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000110111000000000000000000000000000000000000000000000000', 8),
                23,
                0.0000000000000001,
            ],
            [
                $this->bin('0011111110001000000000000000000000000000000000000000000000000000', 8),
                0.01171875,
                0.00000001,
            ],
            [
                $this->bin('0000000000000000000000000000000000000000000000000000000000000001', 8),
                4.9406564584124654 * 10 ** -324,
                10 ** -324,
            ],
            [
                $this->bin('0000000000001111111111111111111111111111111111111111111111111111', 8),
                2.2250738585072009 * 10 ** -308,
                10 ** -308,
            ],
            [
                $this->bin('0000000000010000000000000000000000000000000000000000000000000000', 8),
                2.2250738585072014 * 10 ** -308,
                10 ** -308,
            ],
            [
                $this->bin('0111111111101111111111111111111111111111111111111111111111111111', 8),
                1.7976931348623157 * 10 ** 308,
                1,
            ],
            [
                $this->bin('0000000000000000000000000000000000000000000000000000000000000000', 8),
                0,
                0.0000000000000001,
            ],
            [
                $this->bin('1000000000000000000000000000000000000000000000000000000000000000', 8),
                -0,
                0.0000000000000001,
            ],
            [$this->bin('0111111111110000000000000000000000000000000000000000000000000000', 8), INF, 1],
            [$this->bin('1111111111110000000000000000000000000000000000000000000000000000', 8), -INF, 1],
            [
                $this->bin('0011111111010101010101010101010101010101010101010101010101010101', 8),
                1 / 3,
                0.0000000000000001,
            ],
            [
                $this->bin('0100000000001001001000011111101101010100010001000010110100011000', 8),
                M_PI,
                0.0000000000000001,
            ],
        ];
    }

    private function bin(string $binary, int $length): string
    {
        return str_pad(
            hex2bin(str_pad(base_convert($binary, 2, 16), $length * 2, '0', STR_PAD_LEFT)),
            $length,
            '0',
            STR_PAD_LEFT
        );
    }
}
