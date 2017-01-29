<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use SimplePrepaidCard\CreditCard\Model\FundsWereBlocked;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;
use SimplePrepaidCard\CreditCard\Model\Holder;
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\builders\CreditCard\CreditCardDataBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

class CreditCardControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_create_credit_card()
    {
        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/create-credit-card');

        $this->fillAndSubmitForm('credit_card[submit]', [
            'credit_card[card_number]'       => '4111111111111111',
            'credit_card[card_holder]'       => 'John Doe',
            'credit_card[cvv_code]'          => '123',
            'credit_card[expiry_date_year]'  => '99',
            'credit_card[expiry_date_month]' => '09',
        ]);

        $this->assertRedirectResponse('/customer');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_create_credit_card_with_invalid_request()
    {
        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/create-credit-card');

        $this->fillAndSubmitForm('credit_card[submit]', [
            'credit_card[card_number]'       => 'invalid',
            'credit_card[card_holder]'       => 'John Doe',
            'credit_card[cvv_code]'          => '123',
            'credit_card[expiry_date_year]'  => '99',
            'credit_card[expiry_date_month]' => '09',
        ]);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_not_create_credit_card_with_empty_request()
    {
        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/create-credit-card');

        $this->fillAndSubmitForm('credit_card[submit]', []);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_load_funds()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID))
        );

        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/load-funds');

        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => '100']);

        $this->assertRedirectResponse('/customer');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_load_funds_with_invalid_request()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID))
        );

        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/load-funds');

        $this->fillAndSubmitForm('amount[submit]', ['amount[amount]' => '-100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_get_statement()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::fromString(Holder::HOLDER_ID);

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->ofHolder($holderId)
        );

        $this->given(
            new CreditCardWasCreated($creditCardId, $holderId, CreditCardDataBuilder::create()->build(), Money::GBP(0), Money::GBP(0), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId, $holderId, Money::GBP(100), "Credit card holder's deposit", Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-02')),
            new FundsWereBlocked($creditCardId, $holderId, Money::GBP(1), Money::GBP(100), Money::GBP(99), new \DateTime('2017-01-03')),
            new FundsWereCharged($creditCardId, $holderId, Money::GBP(1), "Credit card holder's charge", Money::GBP(99), Money::GBP(99), new \DateTime('2017-01-04'))
        );

        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/statement');

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertContains('<table', $this->responseContent());
    }

    /** @test */
    public function it_can_get_statement_when_no_statement()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::fromString(Holder::HOLDER_ID);

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->ofHolder($holderId)
        );

        $this->authenticateWithRole('ROLE_HOLDER');
        $this->request('GET', '/statement');

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertContains('<table', $this->responseContent());
    }

    /** @test @dataProvider wrongRoles */
    public function user_with_wrong_role_can_not_access_credit_card_controller(string $uri, string $role)
    {
        $this->authenticateWithRole($role);

        $this->request('GET', $uri);

        $this->assertResponseStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function wrongRoles(): array
    {
        return [
            ['/create-credit-card', 'ROLE_MERCHANT'],
            ['/load-funds', 'ROLE_MERCHANT'],
            ['/statement', 'ROLE_MERCHANT'],
        ];
    }
}
