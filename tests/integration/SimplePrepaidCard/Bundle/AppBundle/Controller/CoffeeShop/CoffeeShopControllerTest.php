<?php

declare(strict_types=1);

namespace integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CoffeeShop;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CoffeeShop\CustomerBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;
use tests\testServices\TestCreditCardProvider;

class CoffeeShopControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_buy_a_product()
    {
        $this->creditCardProvider()->willApprove();
        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId(Uuid::fromString('5a29e675-1c05-4323-ae72-9ffbbb17ad38'))
        );

        $this->request('GET', '/buy-product');

        $this->fillAndSubmitForm('product[buy]', []);

        $this->assertResponseStatusCode(Response::HTTP_FOUND);
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_buy_a_product_when_authorization_request_was_declined()
    {
        $this->creditCardProvider()->willDecline();
        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId(Uuid::fromString('5a29e675-1c05-4323-ae72-9ffbbb17ad38'))
        );

        $this->request('GET', '/buy-product');

        $this->fillAndSubmitForm('product[buy]', []);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    private function creditCardProvider(): TestCreditCardProvider
    {
        return  $this->container()->get('simple_prepaid_card.coffee_shop.credit_card_provider.test');
    }
}
