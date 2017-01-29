<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CoffeeShop\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Application\Query\MerchantView;
use SimplePrepaidCard\CoffeeShop\Infrastructure\DoctrineORMMerchantQuery;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMMerchantQueryTest extends DatabaseTestCase
{
    /** @var DoctrineORMMerchantQuery */
    private $query;

    /** @test */
    public function it_can_get_merchant_view()
    {
        $merchantId = Uuid::fromString(Merchant::MERCHANT_ID);

        $this->buildPersisted(
            MerchantBuilder::create()
                ->withCaptured(Money::GBP(101))
                ->authorizedTo(Money::GBP(1))
                ->withMerchantId($merchantId)
        );

        $this->flushAndClear();

        $this->assertEquals(
            new MerchantView($merchantId, Money::GBP(1), Money::GBP(101)),
            $this->query->get()
        );
    }

    /** @test */
    public function it_can_get_empty_merchant_view()
    {
        $this->assertEquals(
            new MerchantView(),
            $this->query->get()
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->container()->get('simple_prepaid_card.coffee_shop.query.merchant.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
