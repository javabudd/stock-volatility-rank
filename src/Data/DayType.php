<?php

namespace IVRank\Data;

class DayType
{
    public const DAY_TYPE_10  = '10-Day';
    public const DAY_TYPE_20  = '20-Day';
    public const DAY_TYPE_30  = '30-Day';
    public const DAY_TYPE_60  = '60-Day';
    public const DAY_TYPE_90  = '90-Day';
    public const DAY_TYPE_120 = '120-Day';
    public const DAY_TYPE_150 = '150-Day';
    public const DAY_TYPE_180 = '180-Day';

    private const DAY_TYPES = [
        self::DAY_TYPE_10,
        self::DAY_TYPE_20,
        self::DAY_TYPE_30,
        self::DAY_TYPE_60,
        self::DAY_TYPE_90,
        self::DAY_TYPE_120,
        self::DAY_TYPE_150,
        self::DAY_TYPE_180,
    ];

    private string $dayType;

    public function __construct(string $dayType)
    {
        if (!\in_array($dayType, self::DAY_TYPES)) {
            throw new \RuntimeException('Day Type invalid');
        }

        $this->dayType = $dayType;
    }

    public function __toString(): string
    {
        return $this->dayType;
    }
}
