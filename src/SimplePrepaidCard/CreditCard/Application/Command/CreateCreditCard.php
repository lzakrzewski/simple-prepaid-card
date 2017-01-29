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
    public $holder;

    /** @var string */
    public $number;

    /** @var string */
    public $cvv;

    /** @var int */
    public $expiryDateYear;

    /** @var int */
    public $expiryDateMonth;

    public function __construct(
        UuidInterface $creditCardId,
        UuidInterface $holderId,
        string $holder,
        string $number,
        int $cvv,
        int $expiryDateYear,
        int $expiryDateMonth
    ) {
        $this->creditCardId = $creditCardId;
        $this->holderId     = $holderId;

        $this->holder          = $holder;
        $this->number          = $number;
        $this->cvv             = $cvv;
        $this->expiryDateYear  = $expiryDateYear;
        $this->expiryDateMonth = $expiryDateMonth;
    }
}
