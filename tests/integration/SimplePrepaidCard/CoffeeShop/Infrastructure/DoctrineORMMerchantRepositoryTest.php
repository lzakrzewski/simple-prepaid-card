<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CoffeeShop\Infrastructure;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Infrastructure\DoctrineORMMerchantRepository;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\MerchantDoesNotExist;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMMerchantRepositoryTest extends DatabaseTestCase
{
    /** @var DoctrineORMMerchantRepository */
    private $repository;

    /** @test */
    public function it_can_get_merchant_by_id()
    {
        $this->repository->add(Merchant::create());

        $this->flushAndClear();

        $merchant = $this->repository->get(Uuid::fromString(Merchant::MERCHANT_ID));

        $this->assertEquals(Uuid::fromString(Merchant::MERCHANT_ID), $merchant->merchantId());
    }

    /** @test */
    public function it_fails_when_merchant_does_not_exist()
    {
        $this->expectException(MerchantDoesNotExist::class);

        $this->repository->get(Uuid::fromString(Merchant::MERCHANT_ID));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->container()->get('simple_prepaid_card.coffee_shop.repository.merchant.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->repository = null;

        parent::tearDown();
    }
}
