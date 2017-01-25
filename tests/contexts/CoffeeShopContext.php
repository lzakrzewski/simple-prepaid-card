<?php

declare(strict_types=1);

namespace tests\contexts;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Application\Command\BuyProduct;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\CustomerDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\ProductIsUnknown;
use SimplePrepaidCard\CoffeeShop\Model\ProductWasBought;
use tests\builders\CoffeeShop\CustomerBuilder;
use tests\testServices\TestCreditCardProvider;

class CoffeeShopContext extends DefaultContext
{
    /** @var UuidInterface */
    private $customerId;

    /** @BeforeScenario */
    public function resetCustomerId()
    {
        $this->customerId = null;
    }

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
     * @Then I should be notified that product was bought
     */
    public function iShouldBeNotifiedThatProductWasBought()
    {
        $this->expectEvent(ProductWasBought::class);
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
     * @Then I should be notified that merchant was authorized
     */
    public function iShouldBeNotifiedThatMerchantWasAuthorized()
    {
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

    private function creditCardProvider(): TestCreditCardProvider
    {
        return $this->getContainer()->get('simple_prepaid_card.coffee_shop.credit_card_provider.test');
    }
}
