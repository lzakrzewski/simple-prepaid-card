<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CoffeeShop\Infrastructure\ACL;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Infrastructure\ACL\LocalCreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CaptureWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\RefundWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\ReverseWasDeclined;
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
    public function it_can_decline_when_no_credit_card_during_authorization_request()
    {
        $this->expectException(AuthorizationRequestWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->provider->authorizationRequest($customerId, Product::coffee());
    }

    /** @test */
    public function it_can_accept_capture()
    {
        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withAvailableBalance(Money::GBP(1))
                ->withBalance(Money::GBP(1000))
        );

        $this->provider->capture(Money::GBP(999), $customerId);
    }

    /** @test */
    public function it_can_decline_capture()
    {
        $this->expectException(CaptureWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withAvailableBalance(Money::GBP(1))
                ->withBalance(Money::GBP(1000))
        );

        $this->provider->capture(Money::GBP(1001), $customerId);
    }

    /** @test */
    public function it_can_decline_capture_when_no_credit_card_during_capture()
    {
        $this->expectException(CaptureWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->provider->capture(Money::GBP(100), $customerId);
    }

    /** @test */
    public function it_can_accept_reverse()
    {
        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withAvailableBalance(Money::GBP(1))
                ->withBalance(Money::GBP(1000))
        );

        $this->provider->reverse(Money::GBP(999), $customerId);
    }

    /** @test */
    public function it_can_decline_reverse_when_no_credit_card_during_reverse()
    {
        $this->expectException(ReverseWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->provider->reverse(Money::GBP(100), $customerId);
    }

    /** @test */
    public function it_can_accept_refund()
    {
        $customerId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder($customerId)
                ->withAvailableBalance(Money::GBP(1))
                ->withBalance(Money::GBP(1))
        );

        $this->provider->refund(Money::GBP(999), $customerId);
    }

    /** @test */
    public function it_can_decline_refund_when_no_credit_card_during_refund()
    {
        $this->expectException(RefundWasDeclined::class);

        $customerId = Uuid::uuid4();

        $this->provider->refund(Money::GBP(100), $customerId);
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
