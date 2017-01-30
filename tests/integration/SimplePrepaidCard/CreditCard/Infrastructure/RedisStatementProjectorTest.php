<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Infrastructure\RedisStatementQuery;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use SimplePrepaidCard\CreditCard\Model\FundsWereBlocked;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\builders\CreditCard\CreditCardDataBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class RedisStatementProjectorTest extends DatabaseTestCase
{
    /** @var RedisStatementQuery */
    private $query;

    /** @test **/
    public function it_applies_that_funds_were_loaded()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));

        $this->given(
            new FundsWereLoaded($creditCardId, $holderId, Money::GBP(100), 'Funds were loaded', Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_that_funds_were_charged()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));

        $this->given(
            new FundsWereCharged($creditCardId, $holderId, Money::GBP(100), 'Funds were charged', Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_multiple_events()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));

        $this->given(
            new CreditCardWasCreated($creditCardId, $holderId, CreditCardDataBuilder::create()->build(), Money::GBP(0), Money::GBP(0), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId, $holderId, Money::GBP(100), 'Funds were loaded', Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-02')),
            new FundsWereBlocked($creditCardId, $holderId, Money::GBP(1), Money::GBP(100), Money::GBP(99), new \DateTime('2017-01-03')),
            new FundsWereCharged($creditCardId, $holderId, Money::GBP(1), 'Funds were charged', Money::GBP(99), Money::GBP(99), new \DateTime('2017-01-04'))
        );

        $this->assertEquals(
            [
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-04'), 'Funds were charged', Money::GBP(1), Money::GBP(99), Money::GBP(99)),
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test **/
    public function it_applies_on_multiple_statements()
    {
        $creditCardId1 = Uuid::uuid4();
        $holderId1     = Uuid::uuid4();
        $creditCardId2 = Uuid::uuid4();
        $holderId2     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId1)->ofHolder($holderId1));
        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId2)->ofHolder($holderId2));

        $this->given(
            new FundsWereLoaded($creditCardId1, $holderId1, Money::GBP(100), 'Statement 1', Money::GBP(100), Money::GBP(100), new \DateTime('2017-01-01')),
            new FundsWereLoaded($creditCardId2, $holderId2, Money::GBP(200), 'Statement 2', Money::GBP(200), Money::GBP(200), new \DateTime('2018-01-01'))
        );

        $this->assertEquals(
            [
                new StatementView($creditCardId1, $holderId1, new \DateTime('2017-01-01'), 'Statement 1', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId1)
        );

        $this->assertEquals(
            [
                new StatementView($creditCardId2, $holderId2, new \DateTime('2018-01-01'), 'Statement 2', Money::GBP(200), Money::GBP(200), Money::GBP(200)),
            ],
            $this->query->get($creditCardId2)
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->container()->get('simple_prepaid_card.credit_card.query.statement.redis');
    }

    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
