<?php

namespace IVRank\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="metric_type")
 * @ORM\Entity
 *
 * INSERT INTO `metric_type` (`name`, `day_type`)
VALUES
("parkinson-hv", "10-day"),
("parkinson-hv", "20-day"),
("parkinson-hv", "30-day"),
("parkinson-hv", "60-day"),
("parkinson-hv", "90-day"),
("parkinson-hv", "120-day"),
("parkinson-hv", "150-day"),
("parkinson-hv", "180-day") ;

INSERT INTO `metric_type` (`name`, `day_type`)
VALUES
("iv-mean", "10-day"),
("iv-mean", "20-day"),
("iv-mean", "30-day"),
("iv-mean", "60-day"),
("iv-mean", "90-day"),
("iv-mean", "120-day"),
("iv-mean", "150-day"),
("iv-mean", "180-day") ;
 *
 */
class MetricType
{
    public const METRIC_TYPE_PARKINSON_HV = 'parkinson-hv';
    public const METRIC_TYPE_IV_MEAN      = 'iv-mean';

    /**
     * @ORM\Column(
     *     name="id",
     *     type="integer",
     *     length=10,
     *     nullable=false,
     *     options={"unsigned": true}
     * )
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public int $id;

    /**
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    public string $name;

    /**
     * @ORM\Column(name="day_type", type="string", length=20, nullable=false)
     */
    public string $dayType;
}
