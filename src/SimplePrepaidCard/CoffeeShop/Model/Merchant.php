<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

//Todo: ORM for money && UUid
/**
 * @ORM\Entity(repositoryClass="SimplePrepaidCard\CoffeeShop\Infrastructure\DoctrineORMMerchantRepository")
 * @ORM\Table
 */
final class Merchant implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    const MERCHANT_ID = '49ce95dc-bb15-4c45-9df4-7b8c0a9f8896';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="guid")
     *
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $authorized;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", nullable=true)
     */
    private $authorizedBy;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $captured;

    public function __construct(UuidInterface $merchantId)
    {
        $this->merchantId = $merchantId->toString();
        $this->authorized = (int) Money::GBP(0)->getAmount();
        $this->captured   = (int) Money::GBP(0)->getAmount();
    }

    public static function create(): self
    {
        return new self(Uuid::fromString(self::MERCHANT_ID));
    }

    public function authorize(Money $amount, UuidInterface $authorizedBy)
    {
        $this->guardNegativeAmount($amount);

        $this->authorized   = (int) $this->authorized()->add($amount)->getAmount();
        $this->authorizedBy = $authorizedBy->toString();

        $this->record(
            new MerchantWasAuthorized(
                $this->merchantId(),
                $this->authorizedBy(),
                $amount,
                $this->authorized(),
                new \DateTime()
            )
        );
    }

    public function capture(Money $amount, CreditCardProvider $creditCardProvider)
    {
        $this->guardNegativeAmount($amount);
        $this->guardAgainstCaptureMoreThanAuthorizedTo($amount);

        $creditCardProvider->capture($amount, $this->authorizedBy());

        $this->authorized = (int) $this->authorized()->subtract($amount)->getAmount();
        $this->captured   = (int) $this->captured()->add($amount)->getAmount();

        $this->record(
            new AuthorizationWasCaptured(
                $this->merchantId(),
                $this->authorizedBy(),
                $amount,
                $this->authorized(),
                $this->captured(),
                new \DateTime())
        );
    }

    public function reverse(Money $amount, CreditCardProvider $creditCardProvider)
    {
        $this->guardNegativeAmount($amount);
        $this->guardAgainstReverseMoreThanAuthorizedTo($amount);

        $creditCardProvider->reverse($amount, $this->authorizedBy());

        $this->authorized = (int) $this->authorized()->subtract($amount)->getAmount();

        $this->record(
            new AuthorizationWasReversed(
                $this->merchantId(),
                $this->authorizedBy(),
                $amount,
                $this->authorized(),
                $this->captured(),
                new \DateTime())
        );
    }

    public function refund(Money $amount, CreditCardProvider $creditCardProvider)
    {
        $this->guardNegativeAmount($amount);
        $this->guardAgainstRefundMoreThanCaptured($amount);

        $creditCardProvider->refund($amount, $this->authorizedBy());

        $this->captured = (int) $this->captured()->subtract($amount)->getAmount();

        $this->record(
            new CapturedWasRefunded(
                $this->merchantId(),
                $this->authorizedBy(),
                $amount,
                $this->authorized(),
                $this->captured(),
                new \DateTime())
        );
    }

    public function authorized(): Money
    {
        return Money::GBP($this->authorized);
    }

    public function merchantId(): UuidInterface
    {
        return Uuid::fromString($this->merchantId);
    }

    public function captured(): Money
    {
        return Money::GBP($this->captured);
    }

    public function authorizedBy()
    {
        if (null === $this->authorizedBy) {
            return;
        }

        return Uuid::fromString($this->authorizedBy);
    }

    private function guardNegativeAmount(Money $amount)
    {
        if ($amount->isNegative()) {
            throw CannotUseNegativeAmount::with($this->merchantId());
        }
    }

    private function guardAgainstCaptureMoreThanAuthorizedTo(Money $amount)
    {
        if (null === $this->authorizedBy() || $this->authorized()->subtract($amount)->isNegative()) {
            throw CannotCaptureMoreThanAuthorized::with($this->merchantId());
        }
    }

    private function guardAgainstReverseMoreThanAuthorizedTo(Money $amount)
    {
        if ($this->authorized()->subtract($amount)->isNegative()) {
            throw CannotReverseMoreThanAuthorized::with($this->merchantId());
        }
    }

    private function guardAgainstRefundMoreThanCaptured(Money $amount)
    {
        if ($this->captured()->subtract($amount)->isNegative()) {
            throw CannotRefundMoreThanCaptured::with($this->merchantId());
        }
    }
}
