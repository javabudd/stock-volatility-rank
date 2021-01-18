<?php

namespace IVRank\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stock_queue")
 * @ORM\Entity
 */
class StockQueue
{
    /**
     * @ORM\Column(name="ticker", type="string", length=20, nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public string $ticker;
}
