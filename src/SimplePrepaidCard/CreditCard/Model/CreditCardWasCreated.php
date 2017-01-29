<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\Common\Model\DomainEvent;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

final class CreditCardWasCreated implements DomainEvent
{
    /** @var UuidInterface */
    private $creditCardId;

    /** @var UuidInterface */
    private $holderId;

    /** @var CreditCardData */
    private $creditCardData;

    /** @var Money */
    private $balance;

    /** @var Money */
    private $availableBalance;

    /** @var \DateTime */
    private $at;

    public function __construct(
        UuidInterface $creditCardId,
        UuidInterface $holderId,
        CreditCardData $creditCardData,
        Money $balance,
        Money $availableBalance,
        \DateTime $at
    ) {
        $this->creditCardId     = $creditCardId;
        $this->holderId         = $holderId;
        $this->creditCardData   = $creditCardData;
        $this->balance          = $balance;
        $this->availableBalance = $availableBalance;
        $this->at               = $at;
    }

    public function creditCardId(): UuidInterface
    {
        return $this->creditCardId;
    }

    public function holderId(): UuidInterface
    {
        return $this->holderId;
    }

    public function creditCardData(): CreditCardData
    {
        return $this->creditCardData;
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

    public function __toString(): string
    {
        return sprintf(
            'Credit card with id "%s" was created with balance "%s" for "%s" at "%s"',
            $this->creditCardId(),
            MoneyDecimalFormatter::create()->format($this->balance()),
            $this->creditCardData()->number(),
            $this->at()->format('Y:m:d h:i a')
        );
    }
}
