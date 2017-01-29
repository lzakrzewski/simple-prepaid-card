<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\Common\Model\DomainEvent;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

final class ProductWasBought implements DomainEvent
{
    /** @var UuidInterface */
    private $customerId;

    /** @var Product */
    private $product;

    /** @var \DateTime */
    private $at;

    public function __construct(UuidInterface $customerId, Product $product, \DateTime $at)
    {
        $this->customerId = $customerId;
        $this->product    = $product;
        $this->at         = $at;
    }

    public function customerId(): UuidInterface
    {
        return $this->customerId;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function at(): \DateTime
    {
        return $this->at;
    }

    public function __toString(): string
    {
        return sprintf(
            'Product "%s" was bought by customer with "%s" for "%s"',
            $this->product()->productId(),
            $this->customerId(),
            MoneyDecimalFormatter::create()->format($this->product()->price())
        );
    }
}
