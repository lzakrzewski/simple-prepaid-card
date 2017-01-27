<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class MerchantWasAuthorized
{
    /** @var UuidInterface */
    private $merchantId;

    /** @var Money */
    private $amount;

    /** @var Money */
    private $authorizedTo;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $merchantId, Money $amount, Money $authorizedTo, \DateTime $at)
    {
        $this->merchantId   = $merchantId;
        $this->amount       = $amount;
        $this->authorizedTo = $authorizedTo;
        $this->at           = $at;
    }

    public function merchantId(): UuidInterface
    {
        return $this->merchantId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function authorizedTo(): Money
    {
        return $this->authorizedTo;
    }

    public function at(): \DateTime
    {
        return $this->at;
    }
}
