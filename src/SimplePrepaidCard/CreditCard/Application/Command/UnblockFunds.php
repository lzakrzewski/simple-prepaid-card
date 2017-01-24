<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class UnblockFunds
{
    /** @var UuidInterface */
    public $creditCardId;

    public function __construct(UuidInterface $creditCardId)
    {
        $this->creditCardId = $creditCardId;
    }
}
