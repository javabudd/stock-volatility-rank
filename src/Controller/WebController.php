<?php

namespace IVRank\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use IVRank\Data\MetricType;
use IVRank\Data\Stock;
use IVRank\Data\StockMetric;
use IVRank\Data\StockQueue;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class WebController extends AbstractActionController
{
    private EntityManager $em;

    public function onDispatch(MvcEvent $e)
    {
        $this->em = $this->getEvent()->getApplication()->getServiceManager()->get(EntityManager::class);

        return parent::onDispatch($e);
    }

    public function indexAction(): ViewModel
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('row')->from(MetricType::class, 'row')->orderBy('row.name, row.dayType', 'DESC');

        return new ViewModel(
            [
                'metricTypes' => $qb->getQuery()->getResult()
            ]
        );
    }

    public function getMetricsAction(): JsonModel
    {
        $ticker     = $this->getRequest()->getPost()->get('ticker');
        $metricType = $this->getRequest()->getPost()->get('metricType');

        if (!$ticker) {
            return new JsonModel(['failure' => 'no ticker provided']);
        }

        $stock = $this->em->getRepository(Stock::class)->findOneBy(['ticker' => $ticker]);

        if (!$stock && !$this->em->find(StockQueue::class, $ticker)) {
            $queue         = new StockQueue();
            $queue->ticker = $ticker;

            $this->em->persist($queue);
            $this->em->flush();

            return new JsonModel(
                [
                    'failure' => 'Could not find stock, check back in a few minutes while we process'
                ]
            );
        }

        $metricType = $this->em->find(MetricType::class, $metricType);

        if (!$metricType) {
            return new JsonModel(['failure' => 'metric type not found']);
        }

        $metricDate = new DateTime('now - 52 weeks');
        $qb         = $this->em->createQueryBuilder();
        $points     = $qb->select('row')
                         ->from(StockMetric::class, 'row')
                         ->where($qb->expr()->eq('row.stock', ':stock'))
                         ->andWhere($qb->expr()->eq('row.metricType', ':metricType'))
                         ->andWhere($qb->expr()->gte('row.metricDate', ':metricDate'))
                         ->setParameters(
                             [
                                 'stock'      => $stock,
                                 'metricType' => $metricType,
                                 'metricDate' => $metricDate
                             ]
                         )->getQuery()->getResult();

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
