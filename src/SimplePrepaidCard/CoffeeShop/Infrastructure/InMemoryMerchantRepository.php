<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure;

use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;

class InMemoryMerchantRepository implements MerchantRepository
{
    public function get(UuidInterface $merchantId)
    {
        // TODO: Implement get() method.
    }
}
