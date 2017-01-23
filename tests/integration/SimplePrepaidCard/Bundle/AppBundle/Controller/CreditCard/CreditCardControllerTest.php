<?php

declare(strict_types=1);

namespace integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Symfony\Component\HttpFoundation\Response;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

//Todo: Better invalid request tests
class CreditCardControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_create_credit_card()
    {
        $this->request(
            'GET',
            '/create-credit-card',
            [
                'card_number' => '4111111111111111',
                'card_holder' => 'John Doe',
                'ccv'         => '123',
                'expires'     => '0919',
            ]
        );

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function it_can_not_create_credit_card_with_invalid_request()
    {
        $this->request('GET', '/create-credit-card', []);

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function it_can_load_funds()
    {
        $this->request(
            'GET',
            '/load-funds',
            [
                'amount' => '100',
            ]
        );

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function it_can_not_load_funds_with_invalid_request()
    {
        $this->request(
            'GET',
            '/load-funds',
            [
                'amount' => '-100',
            ]
        );

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }
}
