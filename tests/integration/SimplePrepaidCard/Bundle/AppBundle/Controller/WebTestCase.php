<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class WebTestCase extends DatabaseTestCase
{
    /** @var Client */
    private $client;

    protected function request(string $method, string $uri, array $parameters = [])
    {
        $this->client->request($method, $uri, $parameters);
    }

    protected function assertResponseStatusCode(int $expected)
    {
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
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
}
