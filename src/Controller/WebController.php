<?php

namespace IVRank\Controller;

use Doctrine\ORM\EntityManager;
use IVRank\Data\MetricType;
use IVRank\Data\Stock;
use IVRank\Data\StockMetric;
use IVRank\Data\StockQueue;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class WebController extends AbstractActionController
{
    public function indexAction(): JsonModel
    {
        /** @var EntityManager $em */
        $em         = $this->getEvent()->getApplication()->getServiceManager()->get(EntityManager::class);
        $ticker     = $this->getRequest()->getQuery()->get('ticker');
        $metricType = $this->getRequest()->getQuery()->get('metric');
        $dayType    = $this->getRequest()->getQuery()->get('dayType');

        if (!$ticker) {
            return new JsonModel(['failure' => 'no ticker provided']);
        }

        $stock = $em->getRepository(Stock::class)->findOneBy(['ticker' => $ticker]);

        if (!$stock) {
            $queue         = new StockQueue();
            $queue->ticker = $ticker;

            $em->persist($queue);
            $em->flush();

            return new JsonModel(
                [
                    'failure' => 'Could not find stock, check back in a few minutes while we process'
                ]
            );
        }

        $metricType = $em->getRepository(MetricType::class)->findOneBy(
            [
                'name'    => $metricType,
                'dayType' => $dayType
            ]
        );

        if (!$metricType) {
            return new JsonModel(['failure' => 'metric type not found']);
        }

        $points = $em->getRepository(StockMetric::class)->findBy(
            [
                'stock'      => $stock,
                'metricType' => $metricType
            ]
        );

        if (\count($points) === 0) {
            return new JsonModel(['failure' => 'no data found for given metric type']);
        }

        $meanLowest = $this->getLowest($points);
        $meanDiff   = ($this->getHighest($points) - $meanLowest);

        if ($meanDiff === 0.00) {
            $meanDiff = 1;
        }

        $meanRank = (\end($points)->metricValue - $meanLowest) / $meanDiff * 100;

        return new JsonModel(
            [
                'stock'   => $stock->ticker,
                'metric'  => $metricType->name,
                'dayType' => $metricType->dayType,
                'rank'    => \round($meanRank, 3)
            ]
        );
    }

    private function getLowest(array $points): float
    {
        return \array_reduce(
            $points,
            static function ($carry, $item) {
                /** @var StockMetric $item */
                if ($item->metricValue < $carry || $carry === null) {
                    return $item->metricValue;
                }

                return $carry ?? 0.00;
            }
        );
    }

    private function getHighest(array $points): float
    {
        return \array_reduce(
            $points,
            static function ($carry, $item) {
                /** @var StockMetric $item */
                if ($item->metricValue > $carry) {
                    return $item->metricValue;
                }

                return $carry ?? 0.00;
            }
        );
    }
}
