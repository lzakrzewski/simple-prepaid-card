<?php

declare(strict_types=1);

namespace tests\builders\CreditCard;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use tests\builders\Builder;

class CreditCardBuilder implements Builder
{
    /** @var UuidInterface */
    private $creditCardId;

    /** @var UuidInterface */
    private $holderId;

    /** @var string */
    private $holderName;

    /** @var Money */
    private $balance;

    /** @var Money */
    private $availableBalance;

    private function __construct(UuidInterface $creditCardId, UuidInterface $holderId, $holderName, Money $balance, Money $availableBalance)
    {
        $this->creditCardId     = $creditCardId;
        $this->holderId         = $holderId;
        $this->holderName       = $holderName;
        $this->balance          = $balance;
        $this->availableBalance = $availableBalance;
    }

    public static function create(): self
    {
        return new self(
            Uuid::uuid4(),
            Uuid::uuid4(),
            'John Doe '.uniqid(),
            Money::GBP(rand(1, 100000)),
            Money::GBP(rand(1, 100000))
        );
    }

    public function build(): CreditCard
    {
        $creditCard = CreditCard::create($this->creditCardId, $this->holderId, $this->holderName);
        $creditCard->loadFunds($this->balance);

        $amountToBlock = $this->balance->subtract($this->availableBalance);

        if ($amountToBlock->isPositive()) {
            $creditCard->blockFunds($amountToBlock);
        }

        $creditCard->eraseMessages();

        return $creditCard;
    }

    public function withCreditCardId(UuidInterface $creditCardId): self
    {
        $copy               = $this->copy();
        $copy->creditCardId = $creditCardId;

        return $copy;
    }

    public function withHolderId(UuidInterface $holderId): self
    {
        $copy           = $this->copy();
        $copy->holderId = $holderId;

        return $copy;
    }

    public function withHolderName(UuidInterface $holderName): self
    {
        $copy             = $this->copy();
        $copy->holderName = $holderName;

        return $copy;
    }

    public function withBalance(Money $balance): self
    {
        $copy          = $this->copy();
        $copy->balance = $balance;

        return $copy;
    }

    public function withAvailableBalance(Money $availableBalance): self
    {
        $copy                   = $this->copy();
        $copy->availableBalance = $availableBalance;

        return $copy;
    }

    private function copy(): self
    {
        return new self($this->creditCardId, $this->holderId, $this->holderName, $this->balance, $this->availableBalance);
    }
}
