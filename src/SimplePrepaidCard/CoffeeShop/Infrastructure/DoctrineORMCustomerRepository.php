<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\CustomerDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\CustomerRepository;

class DoctrineORMCustomerRepository extends EntityRepository implements CustomerRepository
{
    /** {@inheritdoc} */
    public function add(Customer $customer)
    {
        $this->_em->persist($customer);
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $customerId): Customer
    {
        if (null === $customer = $this->findOneBy(['customerId' => $customerId])) {
            throw CustomerDoesNotExist::with($customerId);
        }

        return $customer;
    }
}
