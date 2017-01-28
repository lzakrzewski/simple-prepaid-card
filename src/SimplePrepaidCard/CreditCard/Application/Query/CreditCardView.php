<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class CreditCardView
{
    /** @var UuidInterface */
    public $creditCardId;

    /** @var Money */
    public $balance;

    /** @var Money */
    public $availableBalance;

    public function __construct(UuidInterface $creditCardId = null, Money $balance = null, Money $availableBalance = null)
    {
        $this->creditCardId     = $creditCardId;
        $this->balance          = $balance;
        $this->availableBalance = $availableBalance;
    }
}
