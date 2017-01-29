<?php

declare(strict_types=1);

namespace integration\SimplePrepaidCard\Bundle\AppBundle\FlashBag;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationWasCaptured;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationWasReversed;
use SimplePrepaidCard\CoffeeShop\Model\CapturedWasRefunded;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CoffeeShop\Model\ProductWasBought;
use SimplePrepaidCard\CreditCard\Model\CreditCardWasCreated;
use SimplePrepaidCard\CreditCard\Model\FundsWereBlocked;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;
use SimplePrepaidCard\CreditCard\Model\FundsWereUnblocked;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\builders\CreditCard\CreditCardDataBuilder;
use tests\integration\SimplePrepaidCard\DatabaseTestCase;

class AppendFlashBagWithDomainEventTest extends DatabaseTestCase
{
    /** @var FlashBag */
    private $flashBag;

    /** @test */
    public function it_appends_flashbag_with_credit_card_domain_events()
    {
        $creditCardId = Uuid::uuid4();
        $holderId     = Uuid::uuid4();
        $now          = new \DateTime();
        $amount       = Money::GBP(100);

        $this->given(
            new CreditCardWasCreated($creditCardId, $holderId, CreditCardDataBuilder::create()->build(), $amount, $amount, $now),
            new FundsWereLoaded($creditCardId, $holderId, $amount, $amount, $amount, $now),
            new FundsWereBlocked($creditCardId, $holderId, $amount, $amount, $amount, $now),
            new FundsWereCharged($creditCardId, $holderId, $amount, $amount, $amount, $now),
            new FundsWereUnblocked($creditCardId, $holderId, $amount, $amount, $amount, $now)
        );

        $this->assertThatFlashbagContainsSuccessMessagesCount(5);
    }

    /** @test */
    public function it_appends_flashbag_with_coffee_shop_domain_events()
    {
        $product    = Product::coffee();
        $merchantId = Uuid::fromString(Merchant::MERCHANT_ID);
        $customerId = Uuid::uuid4();
        $now        = new \DateTime();
        $amount     = Money::GBP(100);

        $this->buildPersisted(MerchantBuilder::create()->withMerchantId($merchantId));

        $this->given(
            new ProductWasBought($customerId, $product, $now),
            new AuthorizationWasCaptured($merchantId, $customerId, $amount, $amount, $amount, $now),
            new AuthorizationWasReversed($merchantId, $customerId, $amount, $amount, $amount, $now),
            new CapturedWasRefunded($merchantId, $customerId, $amount, $amount, $amount, $now)
        );

        $this->assertThatFlashbagContainsSuccessMessagesCount(5);
    }

    private function assertThatFlashbagContainsSuccessMessagesCount(int $expectedCount)
    {
        $this->assertCount($expectedCount, $this->flashBag->get('success'));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->flashBag = $this->container()->get('session.flash_bag');
    }

    protected function tearDown()
    {
        $this->flashBag = null;

        parent::tearDown();
    }
}
