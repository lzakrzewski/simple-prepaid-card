<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Form\Extension\DataCollector\FormDataCollector;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;
use tests\testServices\TestCreditCardProvider;

abstract class WebTestCase extends DatabaseTestCase
{
    /** @var Client */
    private $client;

    protected function request(string $method, string $uri, array $parameters = [])
    {
        $this->client->request($method, $uri, $parameters);

        $this->client->enableProfiler();
    }

    protected function fillAndSubmitForm(string $submitButton, array $data = [])
    {
        $form = $this->client
            ->getCrawler()
            ->selectButton($submitButton)->form();

        $this->client->submit($form, $data);
    }

    protected function assertThatFormIsValid()
    {
        $collector = $this->formCollector();
        $response  = $this->responseContent();

        $this->assertEquals(0, $collector->getData()['nb_errors']);
        $this->assertNotContains('has-error', $response);
        $this->assertNotContains('alert-danger', $response);
    }

    protected function assertThatFormIsNotValid()
    {
        $collector = $this->formCollector();
        $response  = $this->responseContent();

        $this->assertFalse(
            0 === (int) $collector->getData()['nb_errors']
            && 0 === strpos($response, 'has-error')
            && 0 === strpos($response, 'alert-danger')
        );
    }

    protected function assertResponseStatusCode(int $expected)
    {
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    protected function responseContent(): string
    {
        return (string) $this->client->getResponse();
    }

    protected function creditCardProvider(): TestCreditCardProvider
    {
        return  $this->container()->get('simple_prepaid_card.coffee_shop.credit_card_provider.test');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->client = null;

        parent::tearDown();
    }

    private function formCollector(): FormDataCollector
    {
        return $this->client->getProfile()->getCollector('form');
    }
}
