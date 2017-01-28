<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardView;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMCreditCardQuery;
use SimplePrepaidCard\CreditCard\Model\Holder;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMCreditCardQueryTest extends DatabaseTestCase
{
    /** @var DoctrineORMCreditCardQuery */
    private $query;

    /** @test  */
    public function it_can_get_credit_card_of_static_card_holder()
    {
        $creditCardId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(55))
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID))
        );

        $this->flushAndClear();

        $this->assertEquals(new CreditCardView($creditCardId, Money::GBP(101), Money::GBP(55)), $this->query->get());
    }

    /** @test  */
    public function it_can_get_last_credit_card_of_static_card_holder()
    {
        $creditCardId = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withBalance(Money::GBP(222))
                ->withAvailableBalance(Money::GBP(22))
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID))
        );

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->withBalance(Money::GBP(101))
                ->withAvailableBalance(Money::GBP(55))
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID))
        );

        $this->flushAndClear();

        $this->assertEquals(new CreditCardView($creditCardId, Money::GBP(101), Money::GBP(55)), $this->query->get());
    }

    /** @test */
    public function it_can_get_null_credit_card_of_static_card_holder_when_no_credit_card()
    {
        $this->assertEquals(new CreditCardView(), $this->query->get());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->container()->get('simple_prepaid_card.credit_card.query.credit_card.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
