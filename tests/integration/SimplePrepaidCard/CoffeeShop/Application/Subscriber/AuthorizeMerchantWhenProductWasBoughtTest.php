<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CoffeeShop\Application\Subscriber;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\MessageBus;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\MerchantDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CoffeeShop\Model\ProductWasBought;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class AuthorizeMerchantWhenProductWasBoughtTest extends DatabaseTestCase
{
    /** @var MessageBus */
    private $eventBus;

    /** @var MerchantRepository */
    private $merchants;

    /** @test */
    public function it_can_authorize_a_merchant_when_product_was_bought()
    {
        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
                ->authorizedTo(Money::GBP(100))
        );

        $this->given(new ProductWasBought(Uuid::uuid4(), Product::coffee(), new \DateTime()));

        $expectedAuthorizedFor = Product::coffee()->price()->add(Money::GBP(100));
        $this->assertThatMerchantWasAuthorizedTo($expectedAuthorizedFor);
    }

    /** @test */
    public function it_fails_when_merchant_does_not_exist()
    {
        $this->expectException(MerchantDoesNotExist::class);

        $this->given(new ProductWasBought(Uuid::uuid4(), Product::coffee(), new \DateTime()));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->eventBus  = $this->container()->get('event_bus');
        $this->merchants = $this->container()->get('simple_prepaid_card.coffee_shop.repository.merchant');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->eventBus  = null;
        $this->merchants = null;
    }

    private function assertThatMerchantWasAuthorizedTo(Money $amount)
    {
        $merchant = $this->merchants->get(Uuid::fromString(Merchant::MERCHANT_ID));

        $this->assertEquals($amount, $merchant->authorized());
    }
}
