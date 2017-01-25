<?php

declare(strict_types=1);

namespace integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

//Todo: Better invalid request tests
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
    public function it_can_block_funds()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(101))
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/block-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_block_funds_with_invalid_request()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(101))
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/block-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '-100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_unblock_funds()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(1))
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/unblock-funds');

        $this->fillAndSubmitForm('save[save]');

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_charge_funds()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(1))
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/charge-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_charge_funds_with_invalid_request()
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(1))
                ->withCreditCardId(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'))
        );

        $this->request('GET', '/charge-funds');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '-100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }
}
