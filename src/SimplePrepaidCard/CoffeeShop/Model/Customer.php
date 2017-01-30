<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CreditCardProvider;
use SimplePrepaidCard\Common\Model\Aggregate;

final class Customer implements ContainsRecordedMessages, Aggregate
{
    use PrivateMessageRecorderCapabilities;

    const CUSTOMER_ID = '5a29e675-1c05-4323-ae72-9ffbbb17ad38';

    /** @var int */
    private $id;

    /** @var string */
    private $customerId;

    /** @var \DateTime */
    private $lastPurchaseAt;

    public function __construct(UuidInterface $customerId)
    {
        $this->customerId = $customerId->toString();
    }

    public static function create(): self
    {
        return new self(Uuid::fromString(self::CUSTOMER_ID));
    }

    public function buyProduct(Product $product, CreditCardProvider $creditCardProvider)
    {
        $creditCardProvider->authorizationRequest($this->customerId(), $product);

        $this->record(new ProductWasBought($this->customerId(), $product, $lastPurchaseAt = new \DateTime()));

        $this->lastPurchaseAt = $lastPurchaseAt;
    }

    public function customerId(): UuidInterface
    {
        return Uuid::fromString($this->customerId);
    }
}
