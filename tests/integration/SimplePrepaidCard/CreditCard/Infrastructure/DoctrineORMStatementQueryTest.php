<?php

declare(strict_types=1);

namespace test\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMStatementQuery;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMStatementQueryTest extends DatabaseTestCase
{
    /** @var DoctrineORMStatementQuery */
    private $query;

    /** @test * */
    public function it_returns_statement()
    {
        $creditCardId = Uuid::uuid4();

        $view3 = $this->persist(new StatementView($creditCardId, new \DateTime('2017-01-01'), 'Funds loaded', 100, 100, 100));
        $view2 = $this->persist(new StatementView($creditCardId, new \DateTime('2017-01-02'), 'Funds loaded', 100, 200, 200));
        $view1 = $this->persist(new StatementView($creditCardId, new \DateTime('2017-01-03'), 'Funds charged', 100, 100, 100));

        $this->assertEquals([$view1, $view2, $view3], $this->query->get($creditCardId));
    }

    /** @test * */
    public function it_does_not_return_statement_of_another_credit_card()
    {
        $creditCardId        = Uuid::uuid4();
        $anotherCreditCardId = Uuid::uuid4();

        $view = $this->persist(new StatementView($creditCardId, new \DateTime('2017-01-01'), 'Funds loaded', 100, 100, 100));
        $this->persist(new StatementView($anotherCreditCardId, new \DateTime('2017-01-02'), 'Funds loaded', 100, 200, 200));

        $this->assertEquals([$view], $this->query->get($creditCardId));
    }

    /** @test * */
    public function it_returns_empty_statement_when_no_events_applied_on_credit_card()
    {
        $this->assertEmpty($this->query->get(Uuid::uuid4()));
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
