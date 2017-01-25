<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class ProductWasBought
{
    /** @var UuidInterface */
    private $customerId;

    /** @var Product */
    private $product;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $customerId, Product $product, \DateTime $at)
    {
        $this->customerId = $customerId;
        $this->product    = $product;
        $this->at         = $at;
    }
}
