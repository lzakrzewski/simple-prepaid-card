<?php

declare(strict_types=1);

namespace tests\testServices;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Lzakrzewski\DoctrineDatabaseBackup\DoctrineDatabaseBackup;

class TestSetup
{
    /** @var DoctrineDatabaseBackup */
    private $backup;

    /** @var ContainsRecordedEventsMiddleware */
    private $recordedEvents;

    public function __construct(DoctrineDatabaseBackup $backup, ContainsRecordedEventsMiddleware $recordedEvents)
    {
        $this->backup         = $backup;
        $this->recordedEvents = $recordedEvents;
    }

    public function setup()
    {
        $this->setupDatabase();
        $this->clearRecordedEvents();
    }

    private function setupDatabase()
    {
        $this->backup->restore(function (EntityManager $entityManager) {
            $params = $entityManager->getConnection()->getParams();

            if (file_exists($params['path'])) {
                $this->backup->getPurger()->purge();

                return;
            }

            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
        });
    }

    private function clearRecordedEvents()
    {
        $this->recordedEvents->clear();
    }
}
