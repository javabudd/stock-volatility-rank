<?php

namespace IVRank\Data;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stock_metric")
 * @ORM\Entity
 */
class StockMetric
{
    /**
     * @ORM\Column(name="id", type="guid", length=20, nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public string $id;

    /**
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id", nullable=false)
     * @ORM\ManyToOne(targetEntity="IVRank\Data\Stock")
     */
    public Stock $stock;

    /**
     * @ORM\JoinColumn(name="metric_type", referencedColumnName="id", nullable=false)
     * @ORM\ManyToOne(targetEntity="IVRank\Data\MetricType")
     */
    public MetricType $metricType;

    /**
     * @ORM\Column(name="metric_value", length=20, type="string", nullable=false)
     */
    public string $metricValue;

    /**
     * @ORM\Column(name="metric_date", length=20, type="datetime_immutable", nullable=false)
     */
    public DateTimeImmutable $metricDate;
}
