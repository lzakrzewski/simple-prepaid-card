<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMStatementQuery;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use SimplePrepaidCard\CreditCard\Model\FundsWereBlocked;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

//Todo: Better descriptions for statement like "coffee was bought"
class DoctrineORMStatementProjectorTest extends DatabaseTestCase
{
    /** @var DoctrineORMStatementQuery */
    private $query;

    /** @test **/
    public function it_applies_that_funds_were_loaded()
    {
        $creditCardId = Uuid::uuid4();

        $this->given(
            new FundsWereLoaded($creditCardId, Money::GBP(100), Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView(1, $creditCardId, new \DateTime('2017-01-01'), 'Funds were loaded', 100, 100, 100),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_that_funds_were_charged()
    {
        $creditCardId = Uuid::uuid4();

        $this->given(
            new FundsWereCharged($creditCardId, Money::GBP(100), Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView(1, $creditCardId, new \DateTime('2017-01-01'), 'Funds were charged', 100, 100, 100),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_multiple_events()
    {
        $creditCardId = Uuid::uuid4();

        $this->given(
            new CreditCardWasCreated($creditCardId, Uuid::uuid4(), 'John Doe', Money::GBP(0), Money::GBP(0), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId, Money::GBP(100), Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-02')),
            new FundsWereBlocked($creditCardId, Money::GBP(1), Money::GBP(100), Money::GBP(99), new \DateTime('2017-01-03')),
            new FundsWereCharged($creditCardId, Money::GBP(1), Money::GBP(99), Money::GBP(99), new \DateTime('2017-01-04'))
        );

        $this->assertEquals(
            [
                new StatementView(2, $creditCardId, new \DateTime('2017-01-04'), 'Funds were charged', 1, 99, 99),
                new StatementView(1, $creditCardId, new \DateTime('2017-01-02'), 'Funds were loaded', 100, 100, 100),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_on_multiple_statements()
    {
        $creditCardId1 = Uuid::uuid4();
        $creditCardId2 = Uuid::uuid4();

        $this->given(
            new FundsWereLoaded($creditCardId1, Money::GBP(100), Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId2, Money::GBP(200), Money::GBP(200), Money::GBP(200), new \DateTime('2018-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView(1, $creditCardId1, new \DateTime('2017-01-01'), 'Funds were loaded', 100, 100, 100),
            ],
            $this->query->get($creditCardId1)
        );

        $this->assertEquals(
            [
                new StatementView(2, $creditCardId2, new \DateTime('2018-01-01'), 'Funds were loaded', 200, 200, 200),
            ],
            $this->query->get($creditCardId2)
        );
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

    private function given(...$events)
    {
        foreach ($events as $event) {
            $this->container()->get('event_bus')->handle($event);
        }
    }
}
