<?php

namespace IVRank\Controller;

use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use IVRank\Context\Rank;
use IVRank\Data\MetricType;
use IVRank\Data\Stock;
use IVRank\Data\StockMetric;
use IVRank\Data\StockQueue;
use Laminas\Console\ColorInterface;
use Laminas\Mvc\Console\Controller\AbstractConsoleController;

class CliController extends AbstractConsoleController implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    public function __construct(ObjectManager $em)
    {
        $this->setObjectManager($em);
    }

    public function scrapeAction(): void
    {
        $ranker    = new Rank();
        $em        = $this->getObjectManager();
        $typeRepo  = $em->getRepository(MetricType::class);
        $stockRepo = $em->getRepository(Stock::class);
        $queueRepo = $em->getRepository(StockQueue::class);
        $start     = \microtime(true);

        $this->getConsole()->writeLine('Starting scrape...', ColorInterface::GREEN);
        while (true) {
            if (\microtime(true) - $start % (60 * 60 * 12) === 0) {
                /** @var Stock $stock */
                foreach ($stockRepo->findAll() as $stock) {
                    if ($em->find(StockQueue::class, $stock->ticker)) {
                        continue;
                    }

                    $queue         = new StockQueue();
                    $queue->ticker = $stock->ticker;

                    $em->persist($queue);
                }

                $em->flush();
            }

            $queues = $queueRepo->findBy([]);

            if (\count($queues) === 0) {
                continue;
            }

            foreach ($queues as $queue) {
                $ticker = $queue->ticker;

                if (!$stock = $stockRepo->findOneBy(['ticker' => $ticker])) {
                    $this->getConsole()->writeLine("Inserting stock {$ticker}", ColorInterface::GREEN);
                    $stock         = new Stock();
                    $stock->ticker = $ticker;

                    $em->persist($stock);
                    $em->flush();
                }

                foreach ($ranker($ticker) as $dayFrame => [$parkinson, $ivMean]) {
                    /** @var MetricType $metricType */
                    $metricType = $typeRepo->findOneBy(
                        [
                            'name'    => MetricType::METRIC_TYPE_PARKINSON_HV,
                            'dayType' => (string)$dayFrame
                        ]
                    );

                    $this->insertStockMetricData($stock, $metricType, $parkinson);

                    /** @var MetricType $metricType */
                    $metricType = $typeRepo->findOneBy(
                        [
                            'name'    => MetricType::METRIC_TYPE_IV_MEAN,
                            'dayType' => (string)$dayFrame
                        ]
                    );

                    $this->insertStockMetricData($stock, $metricType, $ivMean);
                }

                $em->remove($queue);
                $em->flush();
                $em->clear();

                $this->getConsole()->writeLine(
                    "Finished inserting data for {$ticker}",
                    ColorInterface::GREEN
                );
            }
        }
    }

    private function insertStockMetricData(
        Stock $stock,
        MetricType $metricType,
        array $data
    ): void {
        $em         = $this->getObjectManager();
        $metricRepo = $em->getRepository(StockMetric::class);

        foreach ($data as $key => $datum) {
            $criteria = [
                'stock'      => $stock,
                'metricDate' => new DateTimeImmutable($datum['x']),
                'metricType' => $metricType
            ];

            if (!$metricRepo->findOneBy($criteria)) {
                $metric              = new StockMetric();
                $metric->stock       = $stock;
                $metric->metricType  = $metricType;
                $metric->metricDate  = new DateTimeImmutable($datum['x']);
                $metric->metricValue = $datum['value'] ?? 0;

                $em->persist($metric);
            }

            if ($key % 100 === 0) {
                $em->flush();
            }
        }
    }
}
