<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use tests\builders\Builder;

abstract class DatabaseTestCase extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $backup = $this->container()->get('lzakrzewski.doctrine_database_backup');

        $backup->restore(function () {
            $this->setupDatabase();
        });
    }

    private function setupDatabase()
    {
        $em     = $this->entityManager();
        $params = $this->entityManager()->getConnection()->getParams();

        if (file_exists($params['path'])) {
            return;
        }

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    protected function buildPersisted(Builder $builder)
    {
        $object = $builder->build();

        $this->entityManager()->persist($object);
        $this->entityManager()->flush();
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
