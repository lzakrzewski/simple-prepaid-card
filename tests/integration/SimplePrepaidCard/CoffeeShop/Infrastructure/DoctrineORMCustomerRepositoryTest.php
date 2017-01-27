<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CoffeeShop\Infrastructure;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Infrastructure\DoctrineORMCustomerRepository;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\CustomerDoesNotExist;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMCustomerRepositoryTest extends DatabaseTestCase
{
    /** @var DoctrineORMCustomerRepository */
    private $repository;

    /** @test */
    public function it_can_get_customer_by_id()
    {
        $this->repository->add(Customer::create());

        $this->flushAndClear();

        $customer = $this->repository->get(Uuid::fromString(Customer::CUSTOMER_ID));

        $this->assertEquals(Uuid::fromString(Customer::CUSTOMER_ID), $customer->customerId());
    }

    /** @test */
    public function it_fails_when_customer_does_not_exist()
    {
        $this->expectException(CustomerDoesNotExist::class);

        $this->repository->get(Uuid::fromString(Customer::CUSTOMER_ID));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->container()->get('simple_prepaid_card.coffee_shop.repository.customer.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->repository = null;

        parent::tearDown();
    }
}
