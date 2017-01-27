<?php

declare(strict_types=1);

namespace tests\contexts;

use Assert\Assertion;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Application\Command\AuthorizeMerchant;
use SimplePrepaidCard\CoffeeShop\Application\Command\BuyProduct;
use SimplePrepaidCard\CoffeeShop\Application\Command\CaptureAuthorization;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationWasCaptured;
use SimplePrepaidCard\CoffeeShop\Model\CannotCaptureMoreThanAuthorized;
use SimplePrepaidCard\CoffeeShop\Model\CannotUseNegativeAmount;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\CustomerDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\MerchantDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;
use SimplePrepaidCard\CoffeeShop\Model\MerchantWasAuthorized;
use SimplePrepaidCard\CoffeeShop\Model\ProductIsUnknown;
use SimplePrepaidCard\CoffeeShop\Model\ProductWasBought;
use tests\builders\CoffeeShop\CustomerBuilder;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\testServices\TestCreditCardProvider;

//Todo: try to concatenate some steps
class CoffeeShopContext extends DefaultContext
{
    /** @var UuidInterface */
    private $customerId;

    /** @var UuidInterface */
    private $merchantId;

    /**
     * @Given I am a customer with id :customerId
     * @Given there is a customer with id :customerId
     */
    public function iAmACustomerWithId(UuidInterface $customerId)
    {
        $this->customerId = $customerId;

        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId($customerId)
        );
    }

    /**
     * @Given there is a merchant with id :merchantId
     * @Given I am a merchant with id :merchantId authorized to :authorized GBP
     * @Given I am a merchant with id :merchantId authorized to :authorized GBP and :captured GBP captured
     */
    public function thereIsAMerchantWithId(UuidInterface $merchantId, Money $authorized = null, Money $captured = null)
    {
        $this->merchantId = $merchantId;

        $builder = MerchantBuilder::create()
            ->withMerchantId($merchantId);

        if (null !== $authorized) {
            $builder = $builder->authorizedTo($authorized);
        }

        if (null !== $captured) {
            $builder = $builder->withCaptured($captured);
        }

        $this->buildPersisted($builder);
    }

    /**
     * @Given credit card provider will approve authorization request
     * @Given credit card provider will approve capture
     */
    public function creditCardProviderWillApproveAuthorizationRequest()
    {
        $this->creditCardProvider()->willApprove();
    }

    /**
     * @Given credit card provider will decline authorization request
     * @Given credit card provider will decline capture
     */
    public function creditCardProviderWillDeclineAuthorizationRequest()
    {
        $this->creditCardProvider()->willDecline();
    }

    /**
     * @When I buy a product :productId for :amount GBP
     */
    public function iBuyAProductForGbp(string $productId)
    {
        $this->handle(new BuyProduct($this->customerId ?: Uuid::uuid4(), $productId));
    }

    /**
     * @When A customer authorizes a merchant with id :merchantId to :amount GBP
     */
    public function aCustomerAuthorizesAMerchantWithIdToGbp(UuidInterface $merchantId, Money $amount)
    {
        $this->handle(new AuthorizeMerchant($merchantId, $this->customerId ?: Uuid::uuid4(), (int) $amount->getAmount()));
    }

    /**
     * @When I capture :amount GBP from my authorization
     */
    public function iCaptureGbpFromMyAuthorization(Money $amount)
    {
        $this->handle(new CaptureAuthorization($this->merchantId ?: Uuid::uuid4(), (int) $amount->getAmount()));
    }

    /**
     * @Then I should be notified that product was bought
     */
    public function iShouldBeNotifiedThatProductWasBought()
    {
        $this->expectEvent(ProductWasBought::class);
    }

    /**
     * @Then I should be notified that merchant was authorized
     */
    public function iShouldBeNotifiedThatMerchantWasAuthorized()
    {
        $this->expectEvent(MerchantWasAuthorized::class);
    }

    /**
     * @Then I should be notified that authorization was captured
     */
    public function iShouldBeNotifiedThatAuthorizationWasCaptured()
    {
        $this->expectEvent(AuthorizationWasCaptured::class);
    }

    /**
     * @Then I should be notified that authorization request was declined
     */
    public function iShouldBeNotifiedThatAuthorizationRequestWasDeclined()
    {
        $this->expectException(AuthorizationRequestWasDeclined::class);
    }

    /**
     * @Then I should be notified that customer does not exist
     */
    public function iShouldBeNotifiedThatCustomerDoesNotExist()
    {
        $this->expectException(CustomerDoesNotExist::class);
    }

    /**
     * @Then I should be notified that product is unknown
     */
    public function iShouldBeNotifiedThatProductIsUnknown()
    {
        $this->expectException(ProductIsUnknown::class);
    }

    /**
     * @Then I should be notified that I cannot use negative amount
     */
    public function iShouldBeNotifiedThatICannotUseNegativeAmount()
    {
        $this->expectException(CannotUseNegativeAmount::class);
    }

    /**
     * @Then I should be notified that merchant does not exist
     */
    public function iShouldBeNotifiedThatMerchantDoesNotExist()
    {
        $this->expectException(MerchantDoesNotExist::class);
    }

    /**
     * @Then I should be notified that I can not capture more than authorized
     */
    public function iShouldBeNotifiedThatICanNotCaptureMoreThanAuthorized()
    {
        $this->expectException(CannotCaptureMoreThanAuthorized::class);
    }

    /**
     * @Then I should be authorized to :amount GBP
     */
    public function iShouldBeAuthorizedToGbp(Money $amount)
    {
        Assertion::eq($amount, $this->merchants()->get($this->merchantId)->authorized());
    }

    /**
     * @Then I should have captured :amount GBP
     */
    public function iShouldHaveCapturedGbp(Money $amount)
    {
        Assertion::eq($amount, $this->merchants()->get($this->merchantId)->captured());
    }

    /**
     * @Transform :customerId
     */
    public function customerId(string $customerId): UuidInterface
    {
        return Uuid::fromString($customerId);
    }

    /**
     * @Transform :merchantId
     */
    public function merchantId(string $merchantId): UuidInterface
    {
        return Uuid::fromString($merchantId);
    }

    /**
     * @Transform :authorized
     * @Transform :captured
     */
    public function money(string $money): Money
    {
        return Money::GBP((int) $money);
    }

    /** @BeforeScenario */
    public function reset()
    {
        $this->customerId = null;
        $this->merchantId = null;
    }

    private function merchants(): MerchantRepository
    {
        return $this->getContainer()->get('simple_prepaid_card.coffee_shop.repository.merchant');
    }

    private function creditCardProvider(): TestCreditCardProvider
    {
        return $this->getContainer()->get('simple_prepaid_card.coffee_shop.credit_card_provider.test');
    }
}
