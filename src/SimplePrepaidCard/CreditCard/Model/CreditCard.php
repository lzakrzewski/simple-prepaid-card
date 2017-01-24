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
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $availableBalance;

    private function __construct(UuidInterface $creditCardId, UuidInterface $holderId, $holderName)
    {
        $this->creditCardId = $creditCardId->toString();
        $this->holderId     = $holderId->toString();
        $this->holderName   = $holderName;

        $balance = Money::GBP(0);

        $this->balance          = (int) $balance->getAmount();
        $this->availableBalance = (int) $balance->getAmount();

        $this->record(
            new CreditCardWasCreated(
                $creditCardId,
                $holderId,
                $this->holderName,
                $balance,
                $balance,
                new \DateTime()
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
        return Money::GBP($this->balance);
    }

    public function availableBalance(): Money
    {
        return Money::GBP($this->availableBalance);
    }

    public function loadFunds(Money $amount)
    {
        $this->guardAgainstNegativeFunds($amount);

        $this->availableBalance = (int) $this
            ->availableBalance()
            ->add($amount)
            ->getAmount();

        $this->balance = (int) $this
            ->balance()
            ->add($amount)
            ->getAmount();

        $this->record(
            new FundsWereLoaded(
                $this->creditCardId(),
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

        $this->availableBalance = (int) $this
            ->availableBalance()
            ->subtract($amount)
            ->getAmount();

        $this->record(
            new FundsWereBlocked(
                $this->creditCardId(),
                $amount,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function unblock()
    {
        if ($this->availableBalance()->greaterThanOrEqual($this->balance())) {
            return;
        }

        $this->availableBalance = (int) $this->balance()->getAmount();

        $this->record(
            new FundsWereUnblocked(
                $this->creditCardId(),
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    public function charge(Money $amount)
    {
        $this->guardAgainstNegativeFunds($amount);
        $this->guardAgainstChargeMoreFundsThanBlocked($amount);

        $this->balance = (int) $this
            ->balance()
            ->subtract($amount)
            ->getAmount();

        $this->record(
            new FundsWereCharged(
                $this->creditCardId(),
                $amount,
                $this->balance(),
                $this->availableBalance(),
                new \DateTime()
            )
        );
    }

    private function guardAgainstNegativeFunds(Money $amount)
    {
        if ($amount->isNegative()) {
            throw CannotLoadNegativeFunds::with($this->creditCardId());
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
