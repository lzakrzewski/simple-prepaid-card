<?php

declare(strict_types=1);

namespace tests\builders\CoffeeShop;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use tests\builders\Builder;

class CustomerBuilder implements Builder
{
    /** @var UuidInterface */
    private $customerId;

    private function __construct(UuidInterface $customerId)
    {
        $this->customerId = $customerId;
    }

    public static function create(): self
    {
        return new self(Uuid::uuid4());
    }

    public function withCustomerId(UuidInterface $customerId): self
    {
        $copy             = $this->copy();
        $copy->customerId = $customerId;

        return $copy;
    }

    public function build(): Customer
    {
        return new Customer($this->customerId);
    }

    private function copy(): self
    {
        return new self($this->customerId);
    }
}
