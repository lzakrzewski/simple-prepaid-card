<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class FundsWereLoaded
{
    /** @var UuidInterface */
    private $creditCardId;

    /** @var Money */
    private $amount;

    /** @var Money */
    private $balance;

    /** @var Money */
    private $availableBalance;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $creditCardId, Money $amount, Money $balance, Money $availableBalance, \DateTime $at)
    {
        $this->creditCardId     = $creditCardId;
        $this->amount           = $amount;
        $this->balance          = $balance;
        $this->availableBalance = $availableBalance;
        $this->at               = $at;
    }

    public function creditCardId(): UuidInterface
    {
        return $this->creditCardId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function availableBalance(): Money
    {
        return $this->availableBalance;
    }
}
