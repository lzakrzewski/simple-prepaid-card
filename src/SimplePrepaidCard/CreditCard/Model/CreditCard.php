<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

//Todo: move to yml
/**
 * @ORM\Entity(repositoryClass="SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMCreditCardRepository")
 * @ORM\Table
 */
final class CreditCard implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    private $creditCardId;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    private $holderId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $holderName;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    private $balance;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    private $availableBalance;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    private function __construct(UuidInterface $creditCardId, UuidInterface $holderId, $holderName)
    {
        $this->creditCardId = $creditCardId->toString();
        $this->holderId     = $holderId->toString();
        $this->holderName   = $holderName;

        $balance = Money::GBP(0);

        $this->balance          = $balance;
        $this->availableBalance = $balance;
        $this->createdAt        = $createdAt        = new \DateTime();

        $this->record(
            new CreditCardWasCreated(
                $creditCardId,
                $holderId,
                $this->holderName,
                $balance,
                $balance,
                $createdAt
            )
        );
    }

    public static function create(UuidInterface $creditCardId, UuidInterface $holderId, string $holderName): self
    {
        return new self($creditCardId, $holderId, $holderName);
    }

    public function creditCardId(): UuidInterface
    {
        return Uuid::fromString($this->creditCardId);
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function availableBalance(): Money
    {
        return $this->availableBalance;
    }

    public function loadFunds(Money $amount)
    {
        $this->guardAgainstNegativeFunds($amount);

        $this->availableBalance = $this->availableBalance()->add($amount);
        $this->balance          = $this->balance()->add($amount);

        $this->record(
            new FundsWereLoaded(
                $this->creditCardId(),
                $this->holderId(),
                $amount,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function blockFunds(Money $amount)
    {
        $this->guardAgainstNegativeFunds($amount);
        $this->guardAgainstNegativeBalance($amount);

        $this->availableBalance = $this->availableBalance()->subtract($amount);

        $this->record(
            new FundsWereBlocked(
                $this->creditCardId(),
                $this->holderId(),
                $amount,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function unblockFunds(Money $unblocked)
    {
        $this->guardAgainstNegativeFunds($unblocked);

        if ($this->availableBalance()->greaterThanOrEqual($this->balance())) {
            return;
        }

        if ($unblocked->add($this->availableBalance())->greaterThan($this->balance())) {
            $unblocked = $this->balance()->subtract($this->availableBalance());
        }

        $this->availableBalance = $this->availableBalance()->add($unblocked);

        $this->record(
            new FundsWereUnblocked(
                $this->creditCardId(),
                $this->holderId(),
                $unblocked,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function chargeFunds(Money $amount)
    {
        $this->guardAgainstNegativeFunds($amount);
        $this->guardAgainstChargeMoreFundsThanBlocked($amount);

        $this->balance = $this->balance()->subtract($amount);

        $this->record(
            new FundsWereCharged(
                $this->creditCardId(),
                $this->holderId(),
                $amount,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function holderId(): UuidInterface
    {
        return Uuid::fromString($this->holderId);
    }

    private function guardAgainstNegativeFunds(Money $amount)
    {
        if ($amount->isNegative()) {
            throw CannotUseNegativeFunds::with($this->creditCardId());
        }
    }

    private function guardAgainstNegativeBalance(Money $amount)
    {
        if ($this->availableBalance()->subtract($amount)->isNegative()) {
            throw CannotBlockMoreThanAvailableFunds::with($this->creditCardId());
        }
    }

    private function guardAgainstChargeMoreFundsThanBlocked(Money $amount)
    {
        $blocked = $this->balance()->subtract($this->availableBalance());

        if ($blocked->subtract($amount)->isNegative()) {
            throw CannotChargeMoreFundsThanBlocked::with($this->creditCardId());
        }
    }
}
