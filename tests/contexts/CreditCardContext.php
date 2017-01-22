<?php

declare(strict_types=1);

namespace tests\contexts;

use Assert\Assertion;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Command\CreateCreditCard;
use SimplePrepaidCard\CreditCard\Model\CreditCardAlreadyExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use tests\builders\CreditCard\CreditCardBuilder;

class CreditCardContext extends DefaultContext
{
    /**
     * @Given I don't have a credit card
     */
    public function iDonTHaveACreditCard()
    {
    }

    /**
     * @Given I have a credit card with id :creditCardId
     */
    public function iHaveACreditCardWithId(UuidInterface $creditCardId)
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
        );
    }

    /**
     * @When I create a credit card with id :creditCardId and holder name :holderName
     */
    public function iCreateACreditCardWithIdAndHolderName(UuidInterface $creditCardId, string $holderName)
    {
        $this->handle(new CreateCreditCard($creditCardId, Uuid::uuid4(), $holderName));
    }

    /**
     * @Then I should be notified that a credit card was created
     */
    public function iShouldBeNotifiedThatACreditCardWasCreated()
    {
        $this->expectEvent(CreditCardWasCreated::class);
    }

    /**
     * @Then balance of a credit card with id :creditCardId should be :balance
     */
    public function balanceOfACreditCardWithIdShouldBe(UuidInterface $creditCardId, Money $balance)
    {
        Assertion::eq($this->creditCards()->get($creditCardId)->balance(), $balance);
    }

    /**
     * @Then available balance of a credit card with id :creditCardId should be :availableBalance
     */
    public function availableBalanceOfACreditCardWithIdShouldBe(UuidInterface $creditCardId, Money $availableBalance)
    {
        Assertion::eq($this->creditCards()->get($creditCardId)->availableBalance(), $availableBalance);
    }

    /**
     * @Then I should be notified that credit card already exist
     */
    public function iShouldBeNotifiedThatCreditCardAlreadyExist()
    {
        $this->expectException(CreditCardAlreadyExist::class);
    }

    /**
     * @Transform :creditCardId
     */
    public function creditCardId(string $creditCardId): UuidInterface
    {
        return Uuid::fromString($creditCardId);
    }

    /**
     * @Transform :balance
     */
    public function balance(string $balance): Money
    {
        return Money::GBP((int) $balance);
    }

    /**
     * @Transform :availableBalance
     */
    public function availableBalance(string $availableBalance): Money
    {
        return Money::GBP((int) $availableBalance);
    }

    private function creditCards(): CreditCardRepository
    {
        return $this->getContainer()->get('simple_prepaid_card.credit_card.repository.credit_card');
    }
}
