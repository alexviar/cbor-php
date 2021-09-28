<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018-2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace CBOR;

use ArrayIterator;
use function count;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;

/**
 * @phpstan-implements IteratorAggregate<int, MapItem>
 */
final class MapObject extends AbstractCBORObject implements Countable, IteratorAggregate, Normalizable
{
    private const MAJOR_TYPE = self::MAJOR_TYPE_MAP;

    /**
     * @var MapItem[]
     */
    private $data;

    /**
     * @var string|null
     */
    private $length;

    /**
     * @param MapItem[] $data
     */
    public function __construct(array $data = [])
    {
        [$additionalInformation, $length] = LengthCalculator::getLengthOfArray($data);
        array_map(static function ($item): void {
            if (!$item instanceof MapItem) {
                throw new InvalidArgumentException('The list must contain only MapItem objects.');
            }
        }, $data);

        parent::__construct(self::MAJOR_TYPE, $additionalInformation);
        $this->data = $data;
        $this->length = $length;
    }

    /**
     * @param MapItem[] $data
     */
    public static function create(array $data = []): self
    {
        return new self($data);
    }

    public function __toString(): string
    {
        $result = parent::__toString();
        if (null !== $this->length) {
            $result .= $this->length;
        }
        foreach ($this->data as $object) {
            $result .= $object->getKey()->__toString();
            $result .= $object->getValue()->__toString();
        }

        return $result;
    }

    public function add(CBORObject $key, CBORObject $value): self
    {
        $this->data[] = MapItem::create($key, $value);
        [$this->additionalInformation, $this->length] = LengthCalculator::getLengthOfArray($this->data);

        return $this;
    }

    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return Iterator<int, MapItem>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->data);
    }

    public function normalize(): array
    {
        $result = [];
        foreach ($this->data as $object) {
            $keyObject = $object->getKey();
            if (!$keyObject instanceof Normalizable) {
                throw new InvalidArgumentException('Invalid key. Shall be normalizable');
            }
            $valueObject = $object->getValue();
            $result[$keyObject->normalize()] = $valueObject instanceof Normalizable ? $valueObject->normalize() : $object;
        }

        return $result;
    }

    /**
     * @deprecated The method will be removed on v3.0. Please use CBOR\Normalizable interface
     *
     * @return array<int|string, mixed>
     */
    public function getNormalizedData(bool $ignoreTags = false): array
    {
        return $this->normalize();
    }
}
