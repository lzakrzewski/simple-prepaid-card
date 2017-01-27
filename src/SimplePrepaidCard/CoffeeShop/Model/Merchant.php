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
    private $authorizedTo;

    public function __construct(UuidInterface $merchantId)
    {
        $this->merchantId   = $merchantId->toString();
        $this->authorizedTo = (int) Money::GBP(0)->getAmount();
    }

    public static function create(): self
    {
        return new self(Uuid::fromString(self::MERCHANT_ID));
    }

    public function authorize(Money $amount)
    {
        $this->guardNegativeAmount($amount);

        $this->authorizedTo = (int) $this->authorizedTo()->add($amount)->getAmount();

        $this->record(new MerchantWasAuthorized($this->merchantId(), $amount, $this->authorizedTo(), new \DateTime()));
    }

    public function authorizedTo(): Money
    {
        return Money::GBP($this->authorizedTo);
    }

    public function merchantId(): UuidInterface
    {
        return Uuid::fromString($this->merchantId);
    }

    private function guardNegativeAmount(Money $amount)
    {
        if ($amount->isNegative()) {
            throw CannotUseNegativeAmount::with($this->merchantId());
        }
    }
}
