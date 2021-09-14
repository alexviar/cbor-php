<?php

declare(strict_types=1);

namespace CBOR;

use ArrayIterator;
use function count;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;

/**
 * @final
 */
class IndefiniteLengthListObject extends AbstractCBORObject implements Countable, IteratorAggregate
{
    private const MAJOR_TYPE = 0b100;
    private const ADDITIONAL_INFORMATION = 0b00011111;

    /**
     * @var CBORObject[]
     */
    private array $data = [];

    public function __construct()
    {
        parent::__construct(self::MAJOR_TYPE, self::ADDITIONAL_INFORMATION);
    }

    public function __toString(): string
    {
        $result = parent::__toString();
        foreach ($this->data as $object) {
            $result .= (string) $object;
        }
        $bin = hex2bin('FF');
        if (false === $bin) {
            throw new InvalidArgumentException('Unable to convert the data');
        }
        $result .= $bin;

        return $result;
    }

    public function getNormalizedData(bool $ignoreTags = false): array
    {
        return array_map(static function (CBORObject $item) use ($ignoreTags) {
            return $item->getNormalizedData($ignoreTags);
        }, $this->data);
    }

    public function add(CBORObject $item): void
    {
        $this->data[] = $item;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->data);
    }
}
