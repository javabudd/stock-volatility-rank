<?php

namespace IVRank\Context;

use GuzzleHttp\Client;
use IVRank\Data\DayType;

/**
 * IV Rank = (current Parkinson IV – `X-day` Parkinson IV low) / (`X-day` Parkinson IV high – `X-day` Parkinson IV low)
 */
class Rank
{
    private const QUERY_URL = 'https://www.alphaquery.com/data/option-statistic-chart';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function __invoke(string $stockTicker): array
    {
        $dayFrames = [
            new DayType(DayType::DAY_TYPE_10),
            new DayType(DayType::DAY_TYPE_20),
            new DayType(DayType::DAY_TYPE_30),
            new DayType(DayType::DAY_TYPE_60),
            new DayType(DayType::DAY_TYPE_90),
            new DayType(DayType::DAY_TYPE_120),
            new DayType(DayType::DAY_TYPE_150),
            new DayType(DayType::DAY_TYPE_180),
        ];

        $results = [];
        foreach ($dayFrames as $dayFrame) {
            $parkinsonHVPoints = $this->getParkinsonHV($stockTicker, $dayFrame);
            $ivMeanPoints      = $this->getIVMean($stockTicker, $dayFrame);

            $results[(string)$dayFrame] = [
                $parkinsonHVPoints,
                $ivMeanPoints
            ];
        }

        return $results;
    }

    private function getParkinsonHV(string $stockTicker, DayType $dayType): array
    {
        $response = $this->client->get(
            \sprintf(
                '%s?ticker=%s&perType=%s&identifier=' .
                'parkinson-historical-volatility',
                self::QUERY_URL,
                $stockTicker,
                $dayType
            )
        );

        return \json_decode($response->getBody(), true, 512, \JSON_THROW_ON_ERROR);
    }

    private function getIVMean(string $stockTicker, DayType $dayType): array
    {
        $response = $this->client->get(
            \sprintf(
                '%s?ticker=%s&perType=%s&identifier=' .
                'iv-mean',
                self::QUERY_URL,
                $stockTicker,
                $dayType
            )
        );

        return \json_decode($response->getBody(), true, 512, \JSON_THROW_ON_ERROR);
    }
}

