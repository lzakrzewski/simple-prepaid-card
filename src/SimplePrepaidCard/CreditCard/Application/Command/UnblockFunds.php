<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class UnblockFunds
{
    /** @var UuidInterface */
    public $creditCardId;

    /** @var int */
    public $amount;

    public function __construct(UuidInterface $creditCardId, int $amount)
    {
        $this->creditCardId = $creditCardId;
        $this->amount       = $amount;
    }
}
