<?php

declare(strict_types=1);

namespace CBOR;

final class ByteStringObject extends AbstractCBORObject
{
    private const MAJOR_TYPE = 0b010;

    private string $value;
    private ?int $length;

    public function __construct(string $data)
    {
        [$additionalInformation, $length] = LengthCalculator::getLengthOfString($data);

        parent::__construct(self::MAJOR_TYPE, $additionalInformation);
        $this->length = $length;
        $this->value = $data;
    }

    public function __toString(): string
    {
        $result = parent::__toString();
        if (null !== $this->length) {
            $result .= $this->length;
        }
        $result .= $this->value;

        return $result;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLength(): int
    {
        return mb_strlen($this->value, '8bit');
    }

    public function getNormalizedData(bool $ignoreTags = false): string
    {
        return $this->value;
    }
}
