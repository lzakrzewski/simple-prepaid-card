<?php

declare(strict_types=1);

namespace tests\e2e\SimplePrepaidCard;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use tests\builders\CoffeeShop\CustomerBuilder;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

//Todo: change to btn click
class E2ETest extends WebTestCase
{
    /** @test */
    public function holder_can_create_card_and_card_customer_can_buy_product_and_then_merchant_can_capture_authorization()
    {
        $this->markTestIncomplete();

        $this->request('GET', '/create-credit-card');
        $this->fillAndSubmitForm('credit_card[save]', [
            'credit_card[card_number]' => '4111111111111111',
            'credit_card[card_holder]' => 'John Doe',
            'credit_card[ccv]'         => '123',
            'credit_card[expires]'     => '0919',
        ]);

        $this->request('GET', '/load-funds');
        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '550']);

        $this->request('GET', '/buy-product');
        $this->fillAndSubmitForm('product[buy]', []);

        $this->request('GET', '/capture-authorization');
        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '490']);

        $this->assertCreditCardBalance(
            Money::GBP(550)
                ->subtract(Money::GBP(490))
        );
        $this->assertCreditCardAvailableBalance(
            Money::GBP(550)
                ->subtract(Money::GBP(490))
        );
        $this->assertMerchantAuthorized(Money::GBP(500));
        $this->assertMerchantCaptured(Money::GBP(490));
    }

    /** @test */
    public function holder_can_create_card_and_customer_can_buy_product_and_then_merchant_can_reverse_authorization()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function holder_can_create_card_and_customer_can_buy_product_and_then_merchant_can_capture_authorization_and_then_refund()
    {
        $this->markTestIncomplete();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->creditCardProvider()->willApprove();

        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId(Uuid::fromString(Customer::CUSTOMER_ID))
        );

        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );
    }

    private function assertCreditCardBalance(Money $amount)
    {
        $creditCard = $this->creditCard();

        $this->assertEquals($amount, $creditCard->balance());
    }

    private function assertCreditCardAvailableBalance(Money $amount)
    {
        $creditCard = $this->creditCard();

        $this->assertEquals($amount, $creditCard->availableBalance());
    }

    private function assertMerchantAuthorized(Money $amount)
    {
        $merchant = $this->merchant();

        $this->assertEquals($amount, $merchant->authorized());
    }

    private function assertMerchantCaptured(Money $amount)
    {
        $merchant = $this->merchant();

        $this->assertEquals($amount, $merchant->captured());
    }

    private function creditCard(): CreditCard
    {
        return $this->container()
            ->get('simple_prepaid_card.credit_card.repository.credit_card')
            ->get(
                $this->container()
                    ->get('simple_prepaid_card.credit_card.query.credit_card_id_of_holder')
                    ->get(Uuid::fromString(Customer::CUSTOMER_ID))
            );
    }

    private function merchant(): Merchant
    {
        return $this->container()
            ->get('simple_prepaid_card.credit_card.repository.credit_card')
            ->get(Uuid::fromString(Merchant::MERCHANT_ID));
    }
}
