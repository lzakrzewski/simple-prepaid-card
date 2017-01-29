<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;
use SimplePrepaidCard\Common\Model\Aggregate;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass="SimplePrepaidCard\CoffeeShop\Infrastructure\DoctrineORMCustomerRepository")
 * @ORM\Table
 */
final class Customer implements ContainsRecordedMessages, Aggregate
{
    use PrivateMessageRecorderCapabilities;

    const CUSTOMER_ID = '5a29e675-1c05-4323-ae72-9ffbbb17ad38';

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
    private $customerId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
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
