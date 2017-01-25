<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class BuyProduct
{
    /** @var UuidInterface */
    public $customerId;

    /** @var string */
    public $productId;

    public function __construct(UuidInterface $customerId, string $productId)
    {
        $this->productId  = $productId;
        $this->customerId = $customerId;
    }
}
