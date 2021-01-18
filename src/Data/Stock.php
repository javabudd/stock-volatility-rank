<?php

namespace IVRank\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stock")
 * @ORM\Entity
 */
class Stock
{
    /**
     * @ORM\Column(
     *     name="id",
     *     type="integer",
     *     length=10,
     *     nullable=false,
     *     options={"unsigned": true, "autoincrement": true}
     * )
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public int $id;

    /**
     * @ORM\Column(name="ticker", type="string", length=5, nullable=false, unique=true)
     */
    public string $ticker;
}
