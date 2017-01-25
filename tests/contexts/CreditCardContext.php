<?php

declare(strict_types=1);

namespace tests\contexts;

use Assert\Assertion;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Command\BlockFunds;
use SimplePrepaidCard\CreditCard\Application\Command\ChargeFunds;
use SimplePrepaidCard\CreditCard\Application\Command\CreateCreditCard;
use SimplePrepaidCard\CreditCard\Application\Command\LoadFunds;
use SimplePrepaidCard\CreditCard\Application\Command\UnblockFunds;
use SimplePrepaidCard\CreditCard\Model\CannotBlockMoreThanAvailableFunds;
use SimplePrepaidCard\CreditCard\Model\CannotChargeMoreFundsThanBlocked;
use SimplePrepaidCard\CreditCard\Model\CannotUseNegativeFunds;
use SimplePrepaidCard\CreditCard\Model\CreditCardAlreadyExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardDoesNotExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use SimplePrepaidCard\CreditCard\Model\FundsWereBlocked;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;
use SimplePrepaidCard\CreditCard\Model\FundsWereUnblocked;
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
     * @Given I have a credit card with id :creditCardId with balance :balance GBP
     */
    public function iHaveACreditCardWithIdWithBalanceGbp(UuidInterface $creditCardId, Money $balance)
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->withBalance($balance)
        );
    }

    /**
     * @Given I have a credit card with id :creditCardId with balance :balance GBP and available balance :availableBalance GBP
     */
    public function iHaveACreditCardWithIdWithBalanceGbpAndAvailableBalanceGbp(UuidInterface $creditCardId, Money $balance, Money $availableBalance)
    {
        $this->buildPersisted(
            CreditCardBuilder::create()
                ->withCreditCardId($creditCardId)
                ->withBalance($balance)
                ->withAvailableBalance($availableBalance)
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
     * @When I load :amount GBP onto a credit card with id :creditCardId
     */
    public function iLoadGbpOntoACreditCardWithId(Money $amount, UuidInterface $creditCardId)
    {
        $this->handle(new LoadFunds($creditCardId, (int) $amount->getAmount()));
    }

    /**
     * @When I block :amount GBP on a credit card with id :creditCardId
     */
    public function iBlockGbpOnACreditCardWithId(Money $amount, UuidInterface $creditCardId)
    {
        $this->handle(new BlockFunds($creditCardId, (int) $amount->getAmount()));
    }

    /**
     * @When I unblock funds on a credit card with id :creditCardId
     */
    public function iUnblockFundsOnACreditCardWithId(UuidInterface $creditCardId)
    {
        $this->handle(new UnblockFunds($creditCardId));
    }

    /**
     * @When I charge :amount GBP from a credit card with id :creditCardId
     */
    public function iChargeGbpFromACreditCardWithId(Money $amount, UuidInterface $creditCardId)
    {
        $this->handle(new ChargeFunds($creditCardId, (int) $amount->getAmount()));
    }

    /**
     * @Then I should be notified that a credit card was created
     */
    public function iShouldBeNotifiedThatACreditCardWasCreated()
    {
        $this->expectEvent(CreditCardWasCreated::class);
    }

    /**
     * @Then I should be notified that funds were loaded
     */
    public function iShouldBeNotifiedThatFundsWereLoaded()
    {
        $this->expectEvent(FundsWereLoaded::class);
    }

    /**
     * @Then I should be notified that funds were blocked
     */
    public function iShouldBeNotifiedThatFundsWereBlocked()
    {
        $this->expectEvent(FundsWereBlocked::class);
    }

    /**
     * @Then I should be notified that funds were unblocked
     */
    public function iShouldBeNotifiedThatFundsWereUnblocked()
    {
        $this->expectEvent(FundsWereUnblocked::class);
    }

    /**
     * @Then I should be notified that funds were charged
     */
    public function iShouldBeNotifiedThatFundsWereCharged()
    {
        $this->expectEvent(FundsWereCharged::class);
    }

    /**
     * @Then balance of a credit card with id :creditCardId should be :balance GBP
     */
    public function balanceOfACreditCardWithIdShouldBeGbp(UuidInterface $creditCardId, Money $balance)
    {
        Assertion::eq($this->creditCards()->get($creditCardId)->balance(), $balance);
    }

    /**
     * @Then available balance of a credit card with id :creditCardId should be :availableBalance GBP
     */
    public function availableBalanceOfACreditCardWithIdShouldBeGbp(UuidInterface $creditCardId, Money $availableBalance)
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
     * @Then I should be notified that credit card does not exist
     */
    public function iShouldBeNotifiedThatCreditCardDoesNotExist()
    {
        $this->expectException(CreditCardDoesNotExist::class);
    }

    /**
     * @Then I should be notified that I can not block more than available funds
     */
    public function iShouldBeNotifiedThatICanNotBlockMoreThanAvailableFunds()
    {
        $this->expectException(CannotBlockMoreThanAvailableFunds::class);
    }

    /**
     * @Then I should be notified that I can not charge more funds than blocked
     */
    public function iShouldBeNotifiedThatICanNotChargeMoreFundsThanBlocked()
    {
        $this->expectException(CannotChargeMoreFundsThanBlocked::class);
    }

    /**
     * @Then I should be notified that I can not use negative funds
     */
    public function iShouldBeNotifiedThatICanNotUseNegativeFunds()
    {
        $this->expectException(CannotUseNegativeFunds::class);
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
     * @Transform :availableBalance
     */
    public function money(string $money): Money
    {
        return Money::GBP((int) $money);
    }

    private function creditCards(): CreditCardRepository
    {
        return $this->getContainer()->get('simple_prepaid_card.credit_card.repository.credit_card');
    }
}
