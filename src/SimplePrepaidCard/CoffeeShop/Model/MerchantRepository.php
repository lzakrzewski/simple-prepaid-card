<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

interface MerchantRepository
{
    public function get(UuidInterface $merchantId);
}
