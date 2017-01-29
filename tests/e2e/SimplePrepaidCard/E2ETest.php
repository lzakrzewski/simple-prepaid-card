<?php

declare(strict_types=1);

namespace tests\e2e\SimplePrepaidCard;

use Money\Money;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\Product;

class E2ETest extends E2ETestCase
{
    /** @test */
    public function holder_can_create_card_and_card_customer_can_buy_product_and_then_merchant_can_capture_authorization()
    {
        $this->logInWithCredentials('customer', 'customer');
        $this->createCreditCard();
        $this->loadFunds('5.50');
        $this->buyCoffee();
        $this->logOut();
        $this->logInWithCredentials('merchant', 'merchant');
        $this->captureAuthorization('4.90');

        $this->flushAndClear();

        $expectedCreditBalance      = Money::GBP(550)->subtract(Money::GBP(490));
        $expectedAvailableBalance   = Money::GBP(550)->subtract(Product::coffee()->price());
        $expectedMerchantAuthorized = Product::coffee()->price()->subtract(Money::GBP(490));
        $expectedMerchantCaptured   = Money::GBP(490);

        $this->assertCreditCardBalance($expectedCreditBalance);
        $this->assertCreditCardAvailableBalance($expectedAvailableBalance);
        $this->assertMerchantAuthorized($expectedMerchantAuthorized);
        $this->assertMerchantCaptured($expectedMerchantCaptured);
    }

    /** @test */
    public function holder_can_create_card_and_customer_can_buy_product_and_then_merchant_can_reverse_authorization()
    {
        $this->logInWithCredentials('customer', 'customer');
        $this->createCreditCard();
        $this->loadFunds('5.50');
        $this->buyCoffee();
        $this->logOut();
        $this->logInWithCredentials('merchant', 'merchant');
        $this->reverseAuthorization('4.90');

        $this->flushAndClear();

        $expectedCreditBalance      = Money::GBP(550);
        $expectedAvailableBalance   = Money::GBP(550)->subtract(Product::coffee()->price())->add(Money::GBP(490));
        $expectedMerchantAuthorized = Product::coffee()->price()->subtract(Money::GBP(490));
        $expectedMerchantCaptured   = Money::GBP(0);

        $this->assertCreditCardBalance($expectedCreditBalance);
        $this->assertCreditCardAvailableBalance($expectedAvailableBalance);
        $this->assertMerchantAuthorized($expectedMerchantAuthorized);
        $this->assertMerchantCaptured($expectedMerchantCaptured);
    }

    /** @test */
    public function holder_can_create_card_and_customer_can_buy_product_and_then_merchant_can_capture_authorization_and_then_refund()
    {
        $this->logInWithCredentials('customer', 'customer');
        $this->createCreditCard();
        $this->loadFunds('5.50');
        $this->buyCoffee();
        $this->logOut();
        $this->logInWithCredentials('merchant', 'merchant');
        $this->captureAuthorization('4.90');
        $this->refundCaptured('1.00');

        $this->flushAndClear();

        $expectedCreditBalance      = Money::GBP(550)->subtract(Money::GBP(490))->add(Money::GBP(100));
        $expectedAvailableBalance   = Money::GBP(550)->subtract(Product::coffee()->price())->add(Money::GBP(100));
        $expectedMerchantAuthorized = Product::coffee()->price()->subtract(Money::GBP(490));
        $expectedMerchantCaptured   = Money::GBP(490)->subtract(Money::GBP(100));

        $this->assertCreditCardBalance($expectedCreditBalance);
        $this->assertCreditCardAvailableBalance($expectedAvailableBalance);
        $this->assertMerchantAuthorized($expectedMerchantAuthorized);
        $this->assertMerchantCaptured($expectedMerchantCaptured);
    }
}
