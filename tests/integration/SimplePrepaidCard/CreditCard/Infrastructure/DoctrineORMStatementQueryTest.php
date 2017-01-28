<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMStatementQuery;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMStatementQueryTest extends DatabaseTestCase
{
    /** @var DoctrineORMStatementQuery */
    private $query;

    /** @test * */
    public function it_returns_statement_of_holders_credit_card()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));

        $this->persist(new StatementView(1, $creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->persist(new StatementView(2, $creditCardId, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->persist(new StatementView(3, $creditCardId, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->assertEquals(
            [
                new StatementView(3, $creditCardId, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
                new StatementView(2, $creditCardId, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)),
                new StatementView(1, $creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->ofHolder($holderId)
        );
    }

    /** @test * */
    public function it_returns_statement_of_last_holders_credit_card()
    {
        $creditCardId1 = Uuid::uuid4();
        $creditCardId2 = Uuid::uuid4();
        $holderId      = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId1)->ofHolder($holderId));
        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId2)->ofHolder($holderId));

        $this->persist(new StatementView(1, $creditCardId1, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->persist(new StatementView(2, $creditCardId1, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->persist(new StatementView(3, $creditCardId1, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->persist(new StatementView(4, $creditCardId2, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->persist(new StatementView(5, $creditCardId2, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->persist(new StatementView(6, $creditCardId2, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->assertEquals(
            [
                new StatementView(6, $creditCardId2, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
                new StatementView(5, $creditCardId2, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)),
                new StatementView(4, $creditCardId2, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->ofHolder($holderId)
        );
    }

    /** @test * */
    public function it_does_not_return_statement_of_another_holder_credit_card()
    {
        $creditCardId        = Uuid::uuid4();
        $anotherCreditCardId = Uuid::uuid4();

        $holderId        = Uuid::uuid4();
        $anotherHolderId = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));
        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($anotherCreditCardId)->ofHolder($anotherHolderId));

        $this->persist(new StatementView(1, $creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->persist(new StatementView(2, $anotherCreditCardId, $anotherHolderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));

        $this->assertEquals(
            [
                new StatementView(1, $creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->ofHolder($holderId)
        );
    }

    /** @test * */
    public function it_returns_empty_statement_when_no_events_applied_on_holders_credit_card()
    {
        $this->assertEmpty($this->query->ofHolder(Uuid::uuid4()));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->container()->get('simple_prepaid_card.credit_card.query.statement.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
