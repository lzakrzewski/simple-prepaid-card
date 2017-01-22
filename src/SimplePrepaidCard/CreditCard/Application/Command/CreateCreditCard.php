<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class CreateCreditCard
{
    /** @var UuidInterface */
    public $creditCardId;

    /** @var UuidInterface */
    public $holderId;

    /** @var string */
    public $holderName;

    public function __construct(UuidInterface $creditCardId, UuidInterface $holderId, string $holderName)
    {
        $this->creditCardId = $creditCardId;
        $this->holderId     = $holderId;
        $this->holderName   = $holderName;
    }
}
