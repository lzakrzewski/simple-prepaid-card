<?php

declare(strict_types=1);

namespace integration\SimplePrepaidCard\CoffeeShop\Infrastructure\ACL;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Infrastructure\ACL\LocalCreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class LocalCreditCardProviderTest extends DatabaseTestCase
{
    /** @var LocalCreditCardProvider */
    private $provider;

    /** @test */
    public function it_can_accept_authorization_request()
    {
        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withAvailableBalance(Money::GBP(1000))
        );

        $this->provider->authorizationRequest($customerId, Product::coffee());
    }

    /** @test */
    public function it_can_decline_authorization_request()
    {
        $this->expectException(AuthorizationRequestWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withBalance(Money::GBP(499))
        );

        $this->provider->authorizationRequest($customerId, Product::coffee());
    }

    /** @test */
    public function it_fails_when_no_credit_card()
    {
        $this->expectException(\DomainException::class);

        $customerId = Uuid::uuid4();

        $this->provider->authorizationRequest($customerId, Product::coffee());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->provider = $this->container()->get('simple_prepaid_card.coffee_shop.credit_card_provider.local');
    }

    public function tearDown()
    {
        $this->provider = null;

        parent::tearDown();
    }
}
