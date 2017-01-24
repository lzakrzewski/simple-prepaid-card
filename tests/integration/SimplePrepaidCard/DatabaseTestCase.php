<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard;

use Doctrine\ORM\EntityManager;
use tests\builders\Builder;

abstract class DatabaseTestCase extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->container()->get('test_setup')->setup();
    }

    protected function buildPersisted(Builder $builder)
    {
        $object = $builder->build();

        $this->persist($object);
    }

    protected function persist($object)
    {
        $this->entityManager()->persist($object);
        $this->entityManager()->flush();

        return $object;
    }

    protected function flushAndClear()
    {
        $this->entityManager()->flush();
        $this->entityManager()->clear();
    }

    private function entityManager(): EntityManager
    {
        return $this->container()->get('doctrine.orm.default_entity_manager');
    }
}
