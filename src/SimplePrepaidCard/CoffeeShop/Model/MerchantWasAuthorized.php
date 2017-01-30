<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\Common\Model\DomainEvent;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

final class MerchantWasAuthorized implements DomainEvent
{
    /** @var UuidInterface */
    private $merchantId;

    /** @var UuidInterface */
    private $authorizedBy;

    /** @var Money */
    private $amount;

    /** @var Money */
    private $authorized;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $merchantId, UuidInterface $authorizedBy, Money $amount, Money $authorizedTo, \DateTime $at)
    {
        $this->merchantId   = $merchantId;
        $this->authorizedBy = $authorizedBy;
        $this->amount       = $amount;
        $this->authorized   = $authorizedTo;
        $this->at           = $at;
    }

    public function merchantId(): UuidInterface
    {
        return $this->merchantId;
    }

    public function authorizedBy(): UuidInterface
    {
        return $this->authorizedBy;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function authorized(): Money
    {
        return $this->authorized;
    }

    public function at(): \DateTime
    {
        return $this->at;
    }

    public function __toString(): string
    {
        return sprintf(
            'Merchant with id "%s" was authorized to "%s" GBP at "%s".',
            $this->merchantId(),
            MoneyDecimalFormatter::create()->format($this->amount()),
            $this->at()->format('Y:m:d h:i a')
        );
    }
}
