<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

interface CustomerRepository
{
    /**
     * @param UuidInterface $customerId
     *
     * @throws CustomerDoesNotExist
     *
     * @return Customer
     */
    public function get(UuidInterface $customerId): Customer;
}
