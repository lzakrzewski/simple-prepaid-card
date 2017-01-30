<?php

declare(strict_types=1);

namespace tests\e2e\SimplePrepaidCard;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\Bundle\AppBundle\Command\SetupDataCommand;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use SimplePrepaidCard\CreditCard\Model\Holder;
use Symfony\Component\Console\Tester\CommandTester;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

abstract class E2ETestCase extends WebTestCase
{
    protected function logInWithCredentials(string $userName, string $password)
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('Login', ['_username' => $userName, '_password' => $password]);
    }

    protected function createCreditCard()
    {
        $this->request('GET', '/create-credit-card');
        $this->fillAndSubmitForm('credit_card[submit]', [
            'credit_card[card_number]'       => '4111111111111111',
            'credit_card[card_holder]'       => 'John Doe',
            'credit_card[cvv_code]'          => '123',
            'credit_card[expiry_date_month]' => '09',
            'credit_card[expiry_date_year]'  => '99',
        ]);
    }

    protected function loadFunds(string $amountRepresentation)
    {
        $this->request('GET', '/load-funds');
        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => $amountRepresentation]);
    }

    protected function buyCoffee()
    {
        $this->request('GET', '/buy-product');
        $this->fillAndSubmitForm('product[buy]', []);
    }

    protected function logOut()
    {
        $this->request('GET', '/logout');
    }

    protected function captureAuthorization(string $amountRepresentation)
    {
        $this->request('GET', '/capture-authorization');
        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => $amountRepresentation]);
    }

    protected function reverseAuthorization(string $amountRepresentation)
    {
        $this->request('GET', '/reverse-authorization');
        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => $amountRepresentation]);
    }

    protected function refundCaptured(string $amountRepresentation)
    {
        $this->request('GET', '/refund-captured');
        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => $amountRepresentation]);
    }

    protected function assertCreditCardBalance(Money $amount)
    {
        $creditCard = $this->creditCard();

        $this->assertEquals($amount, $creditCard->balance());
    }

    protected function assertCreditCardAvailableBalance(Money $amount)
    {
        $creditCard = $this->creditCard();

        $this->assertEquals($amount, $creditCard->availableBalance());
    }

    protected function assertMerchantAuthorized(Money $amount)
    {
        $merchant = $this->merchant();

        $this->assertEquals($amount, $merchant->authorized());
    }

    protected function assertMerchantCaptured(Money $amount)
    {
        $merchant = $this->merchant();

        $this->assertEquals($amount, $merchant->captured());
    }

    protected function creditCard(): CreditCard
    {
        return $this->container()
            ->get('simple_prepaid_card.credit_card.repository.credit_card')
            ->get(
                $this->container()
                    ->get('simple_prepaid_card.credit_card.query.credit_card_id_of_holder')
                    ->get(Uuid::fromString(Holder::HOLDER_ID))
            );
    }

    protected function merchant(): Merchant
    {
        return $this->container()
            ->get('simple_prepaid_card.coffee_shop.repository.merchant')
            ->get(Uuid::fromString(Merchant::MERCHANT_ID));
    }

    protected function setUp()
    {
        parent::setUp();

        $cli = new SetupDataCommand();
        $cli->setContainer($this->container());

        $commandTester = new CommandTester($cli);
        $commandTester->execute([]);
    }
}
