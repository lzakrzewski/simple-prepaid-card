<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class AuthorizeMerchant
{
    /** @var UuidInterface */
    public $merchantId;

    /** @var UuidInterface */
    public $customerId;

    /** @var int */
    public $amount;

    public function __construct(UuidInterface $merchantId, UuidInterface $customerId, int $amount)
    {
        $this->merchantId = $merchantId;
        $this->customerId = $customerId;
        $this->amount     = $amount;
    }
}
