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

    /** @var TestCreditCardProvider */
    private $creditCardProvider;

    public function __construct(DoctrineDatabaseBackup $backup, ContainsRecordedEventsMiddleware $recordedEvents, TestCreditCardProvider $creditCardProvider)
    {
        $this->backup             = $backup;
        $this->recordedEvents     = $recordedEvents;
        $this->creditCardProvider = $creditCardProvider;
    }

    public function setup()
    {
        $this->setupDatabase();
        $this->clearRecordedEvents();
        $this->resetCreditCardProvider();
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

    private function resetCreditCardProvider()
    {
        $this->creditCardProvider->reset();
    }
}
