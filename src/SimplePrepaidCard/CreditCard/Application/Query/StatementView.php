<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class StatementView
{
    /** @var UuidInterface */
    public $creditCardId;

    /** @var UuidInterface */
    public $holderId;

    /** @var \DateTime */
    public $date;

    /** @var string */
    public $description;

    /** @var Money */
    public $amount;

    /** @var Money */
    public $availableBalance;

    /** @var Money */
    public $balance;

    public function __construct(UuidInterface $creditCardId, UuidInterface $holderId, \DateTime $date, string $description, Money $amount, Money $availableBalance, Money $balance)
    {
        $this->creditCardId     = $creditCardId;
        $this->holderId         = $holderId;
        $this->date             = $date;
        $this->description      = $description;
        $this->amount           = $amount;
        $this->availableBalance = $availableBalance;
        $this->balance          = $balance;
    }
}
