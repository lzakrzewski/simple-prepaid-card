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
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

//Todo: Better invalid request tests, add role holder
class CreditCardControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_create_credit_card()
    {
        $this->request('GET', '/create-credit-card');

        $this->fillAndSubmitForm('credit_card[save]', [
            'credit_card[card_number]' => '4111111111111111',
            'credit_card[card_holder]' => 'John Doe',
            'credit_card[ccv]'         => '123',
            'credit_card[expires]'     => '0919',
        ]);

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_create_credit_card_with_invalid_request()
    {
        $this->request('GET', '/create-credit-card');

        $this->fillAndSubmitForm('credit_card[save]', []);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_load_funds()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/load-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_load_funds_with_invalid_request()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/load-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '-100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_get_statement()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::fromString(Customer::CUSTOMER_ID);

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->ofHolder($holderId)
        );

        $this->given(
            new CreditCardWasCreated($creditCardId, $holderId, 'John Doe', Money::GBP(0), Money::GBP(0), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId, $holderId, Money::GBP(100), Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-02')),
            new FundsWereBlocked($creditCardId, $holderId, Money::GBP(1), Money::GBP(100), Money::GBP(99), new \DateTime('2017-01-03')),
            new FundsWereCharged($creditCardId, $holderId, Money::GBP(1), Money::GBP(99), Money::GBP(99), new \DateTime('2017-01-04'))
        );

        $this->request('GET', '/statement');

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertContains('<table', $this->responseContent());
    }

    /** @test */
    public function it_can_get_statement_when_no_statement()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::fromString(Customer::CUSTOMER_ID);

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->ofHolder($holderId)
        );

        $this->request('GET', '/statement');

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertContains('<table', $this->responseContent());
    }
}
