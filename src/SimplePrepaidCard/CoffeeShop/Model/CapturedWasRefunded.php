<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\Common\Model\DomainEvent;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

final class CapturedWasRefunded implements DomainEvent
{
    /** @var UuidInterface */
    private $merchantId;

    /** @var UuidInterface */
    private $customerId;

    /** @var Money */
    private $amount;

    /** @var Money */
    private $authorized;

    /** @var Money */
    private $captured;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $merchantId, UuidInterface $customerId, Money $amount, Money $authorized, Money $captured, \DateTime $at)
    {
        $this->merchantId = $merchantId;
        $this->customerId = $customerId;
        $this->amount     = $amount;
        $this->authorized = $authorized;
        $this->captured   = $captured;
        $this->at         = $at;
    }

    public function merchantId(): UuidInterface
    {
        return $this->merchantId;
    }

    public function customerId(): UuidInterface
    {
        return $this->customerId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function authorized(): Money
    {
        return $this->authorized;
    }

    public function captured(): Money
    {
        return $this->captured;
    }

    public function at(): \DateTime
    {
        return $this->at;
    }

    public function __toString(): string
    {
        return sprintf(
            'Capture "%s" GBP was refunded by Merchant with id "%s" at "%s".',
            MoneyDecimalFormatter::create()->format($this->amount()),
            $this->merchantId(),
            $this->at()->format('Y:m:d h:i a')
        );
    }
}
