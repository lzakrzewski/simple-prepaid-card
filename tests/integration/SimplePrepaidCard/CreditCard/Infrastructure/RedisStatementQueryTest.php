<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Infrastructure\RedisStatementProjector;
use SimplePrepaidCard\CreditCard\Infrastructure\RedisStatementQuery;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class RedisStatementQueryTest extends DatabaseTestCase
{
    /** @var RedisStatementQuery */
    private $query;

    /** @test * */
    public function it_returns_statement_of_holders_credit_card()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId)->ofHolder($holderId));

        $this->store(new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->store(new StatementView($creditCardId, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->store(new StatementView($creditCardId, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->assertEquals(
            [
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-03'), 'Funds were charged', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)),
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test * */
    public function it_returns_statement_of_last_holders_credit_card_in_descending_order()
    {
        $creditCardId1 = Uuid::uuid4();
        $creditCardId2 = Uuid::uuid4();
        $holderId      = Uuid::uuid4();

        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId1)->ofHolder($holderId));
        $this->buildPersisted(CreditCardBuilder::create()->withCreditCardId($creditCardId2)->ofHolder($holderId));

        $this->store(new StatementView($creditCardId1, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded 1', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->store(new StatementView($creditCardId1, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded 2', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->store(new StatementView($creditCardId1, $holderId, new \DateTime('2017-01-03'), 'Funds were charged 3', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->store(new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded 4', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->store(new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded 5', Money::GBP(100), Money::GBP(200), Money::GBP(200)));
        $this->store(new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-03'), 'Funds were charged 6', Money::GBP(100), Money::GBP(100), Money::GBP(100)));

        $this->assertEquals(
            [
                new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-03'), 'Funds were charged 6', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
                new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-02'), 'Funds were loaded 5', Money::GBP(100), Money::GBP(200), Money::GBP(200)),
                new StatementView($creditCardId2, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded 4', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId2)
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

        $this->store(new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)));
        $this->store(new StatementView($anotherCreditCardId, $anotherHolderId, new \DateTime('2017-01-02'), 'Funds were loaded', Money::GBP(100), Money::GBP(200), Money::GBP(200)));

        $this->assertEquals(
            [
                new StatementView($creditCardId, $holderId, new \DateTime('2017-01-01'), 'Funds were loaded', Money::GBP(100), Money::GBP(100), Money::GBP(100)),
            ],
            $this->query->get($creditCardId)
        );
    }

    /** @test * */
    public function it_returns_empty_statement_when_no_events_applied_on_holders_credit_card()
    {
        $this->assertEmpty($this->query->get(Uuid::uuid4()));
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

    private function store(StatementView $view)
    {
        $redis = $this->container()->get('redis_client');

        $key = Uuid::uuid4();

        $redis->zadd($this->statementKey($view->creditCardId), $view->date->getTimestamp(), $key);
        $redis->set($key, $this->serialize($view));
    }

    private function serialize(StatementView $view): string
    {
        return json_encode([
            'creditCardId'     => $view->creditCardId->toString(),
            'holderId'         => $view->holderId->toString(),
            'at'               => $view->date->format('Y-m-d H:i:s'),
            'reason'           => $view->description,
            'amount'           => $view->amount->jsonSerialize(),
            'availableBalance' => $view->availableBalance->jsonSerialize(),
            'balance'          => $view->balance->jsonSerialize(),
        ]);
    }

    private function statementKey(UuidInterface $creditCardId): string
    {
        return sprintf('%s:%s', RedisStatementProjector::REDIS_KEY, $creditCardId);
    }
}
