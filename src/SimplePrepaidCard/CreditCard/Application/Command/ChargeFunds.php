<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class ChargeFunds
{
    /** @var UuidInterface */
    public $creditCardId;

    /** @var int */
    public $amount;

    /** @var string */
    public $reason;

    public function __construct(UuidInterface $creditCardId, int $amount, string $reason)
    {
        $this->creditCardId = $creditCardId;
        $this->amount       = $amount;
        $this->reason       = $reason;
    }
}
