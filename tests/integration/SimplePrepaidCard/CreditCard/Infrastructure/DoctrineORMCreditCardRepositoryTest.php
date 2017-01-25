<?php

declare(strict_types=1);

namespace test\integration\SimplePrepaidCard\CreditCard\Infrastructure;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Infrastructure\DoctrineORMCreditCardRepository;
use SimplePrepaidCard\CreditCard\Model\CreditCardAlreadyExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardDoesNotExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardOfCardHolderDoesNotExist;
use tests\builders\CreditCard\CreditCardBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class DoctrineORMCreditCardRepositoryTest extends DatabaseTestCase
{
    /** @var DoctrineORMCreditCardRepository */
    private $repository;

    /** @test */
    public function it_can_get_credit_card_with_id()
    {
        $creditCardId = Uuid::uuid4();

        $this->repository->add(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->build()
        );

        $this->flushAndClear();

        $creditCard = $this->repository->get($creditCardId);

        $this->assertEquals($creditCardId, $creditCard->creditCardId());
    }

    /** @test */
    public function it_fails_when_credit_card_does_not_exist()
    {
        $this->expectException(CreditCardDoesNotExist::class);

        $this->repository->get(Uuid::uuid4());
    }

    /** @test */
    public function it_can_not_add_credit_card_twice()
    {
        $this->expectException(CreditCardAlreadyExist::class);

        $creditCardId = Uuid::uuid4();

        $this->repository->add(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->build()
        );

        $this->flushAndClear();

        $this->repository->add(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->build()
        );
    }

    /** @test */
    public function it_can_get_credit_card_id_of_card_holder()
    {
        $expectedCreditCardId = Uuid::uuid4();
        $holderId             = Uuid::uuid4();

        $this->repository->add(
            CreditCardBuilder::create()
                ->withCreditCardId($expectedCreditCardId)
                ->ofHolder($holderId)
                ->build()
        );

        $this->flushAndClear();

        $this->assertEquals($expectedCreditCardId, $this->repository->creditCardIdOfHolder($holderId));
    }

    /** @test */
    public function it_fails_when_credit_card_of_card_holder_does_not_exist()
    {
        $this->expectException(CreditCardOfCardHolderDoesNotExist::class);

        $this->repository->creditCardIdOfHolder(Uuid::uuid4());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->container()->get('simple_prepaid_card.credit_card.repository.credit_card.doctrine_orm');
    }

    protected function tearDown()
    {
        $this->repository = null;

        parent::tearDown();
    }
}
