<?php

declare(strict_types=1);

namespace CBOR;

abstract class OtherObject extends AbstractCBORObject
{
    private const MAJOR_TYPE = 0b111;

    protected ?string $data;

    public function __construct(int $additionalInformation, ?string $data)
    {
        parent::__construct(self::MAJOR_TYPE, $additionalInformation);
        $this->data = $data;
    }

    public function __toString(): string
    {
        $result = parent::__toString();
        if (null !== $this->data) {
            $result .= $this->data;
        }

        return $result;
    }

    /**
     * @return int[]
     */
    abstract public static function supportedAdditionalInformation(): array;

    abstract public static function createFromLoadedData(int $additionalInformation, ?string $data): self;
}
