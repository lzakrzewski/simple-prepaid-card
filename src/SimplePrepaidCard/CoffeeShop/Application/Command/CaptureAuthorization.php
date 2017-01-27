<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class CaptureAuthorization
{
    /** @var UuidInterface */
    public $merchantId;

    /** @var int */
    public $amount;

    public function __construct(UuidInterface $merchantId, int $amount)
    {
        $this->merchantId = $merchantId;
        $this->amount     = $amount;
    }
}
