<?php

declare(strict_types=1);

namespace tests\contexts;

use Assert\Assertion;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Application\Command\AuthorizeMerchant;
use SimplePrepaidCard\CoffeeShop\Application\Command\BuyProduct;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
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
     */
    public function thereIsAMerchantWithId(UuidInterface $merchantId)
    {
        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId($merchantId)
        );
    }

    /**
     * @Given I am a merchant with id :merchantId authorized to :amount GBP
     */
    public function iAmAMerchantWithIdAuthorizedToGbp(UuidInterface $merchantId, Money $amount)
    {
        $this->merchantId = $merchantId;

        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId($merchantId)
                ->authorizedTo($amount)
        );
    }

    /**
     * @Given credit card provider will approve authorization request for :amount GBP
     */
    public function creditCardProviderWillApproveAuthorizationRequestForGbp(Money $amount)
    {
        $this->creditCardProvider()->willApprove();
    }

    /**
     * @Given credit card provider will decline authorization request for :amount GBP
     */
    public function creditCardProviderWillDeclineAuthorizationRequestForGbp(Money $amount)
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
     * @When I authorize merchant with id :merchantId to :amount GBP
     */
    public function iAuthorizeMerchantWithIdToGbp(UuidInterface $merchantId, Money $amount)
    {
        $this->handle(new AuthorizeMerchant($merchantId, $this->customerId ?: Uuid::uuid4(), (int) $amount->getAmount()));
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
     * @Then I should be authorized to :amount GBP
     */
    public function iShouldBeAuthorizedToGbp(Money $amount)
    {
        Assertion::eq($amount, $this->merchants()->get($this->merchantId)->authorized());
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
