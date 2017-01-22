<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    /** @var ContainerInterface */
    private $container;

    protected function container(): ContainerInterface
    {
        return $this->container;
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->container = $this->createClient()->getContainer();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->container = null;

        parent::tearDown();
    }
}
