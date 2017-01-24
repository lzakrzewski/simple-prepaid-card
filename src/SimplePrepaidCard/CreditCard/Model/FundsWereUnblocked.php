<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class FundsWereUnblocked
{
    /** @var UuidInterface */
    private $creditCardId;

    /** @var Money */
    private $balance;

    /** @var Money */
    private $availableBalance;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $creditCardId, Money $balance, Money $availableBalance, \DateTime $at)
    {
        $this->creditCardId     = $creditCardId;
        $this->balance          = $balance;
        $this->availableBalance = $availableBalance;
        $this->at               = $at;
    }

    public function creditCardId(): UuidInterface
    {
        return $this->creditCardId;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function availableBalance(): Money
    {
        return $this->availableBalance;
    }

    public function at(): \DateTime
    {
        return $this->at;
    }
}
