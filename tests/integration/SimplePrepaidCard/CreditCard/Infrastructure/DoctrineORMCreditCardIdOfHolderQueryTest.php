<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardOfHolderDoesNotExist;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMCreditCardIdOfHolderQuery;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMCreditCardIdOfHolderQueryTest extends DatabaseTestCase
{
    /** @var DoctrineORMCreditCardIdOfHolderQuery */
    private $query;

    /** @test  */
    public function it_can_get_credit_card_id_of_card_holder()
    {
        $expectedCreditCardId = Uuid::uuid4();
        $holderId             = Uuid::uuid4();

        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($expectedCreditCardId)
                ->ofHolder($holderId)
        );

        $this->flushAndClear();

        $this->assertEquals($expectedCreditCardId, $this->query->get($holderId));
    }

    /** @test */
    public function it_fails_when_credit_card_of_card_holder_does_not_exist()
    {
        $this->expectException(CreditCardOfHolderDoesNotExist::class);

        $this->query->get(Uuid::uuid4());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->container()->get('simple_prepaid_card.credit_card.query.credit_card_id_of_holder.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
