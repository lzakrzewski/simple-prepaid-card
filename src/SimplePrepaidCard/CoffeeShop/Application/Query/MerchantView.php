<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Query;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class MerchantView
{
    /** @var UuidInterface */
    public $merchantId;

    /** @var Money */
    public $authorized;

    /** @var Money */
    public $captured;

    public function __construct(UuidInterface $merchantId = null, Money $authorized = null, Money $captured = null)
    {
        $this->merchantId = $merchantId;
        $this->authorized = $authorized;
        $this->captured   = $captured;
    }
}
