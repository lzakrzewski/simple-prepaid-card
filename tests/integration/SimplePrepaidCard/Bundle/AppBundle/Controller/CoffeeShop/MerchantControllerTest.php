<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CoffeeShop;

use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

class MerchantControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_render_merchant_page()
    {
        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/merchant');

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function it_can_capture_authorization()
    {
        $this->creditCardProvider()->willApprove();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/capture-authorization');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertRedirectResponse('/merchant');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_capture_authorization_when_capture_was_declined()
    {
        $this->creditCardProvider()->willDecline();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/capture-authorization');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_reverse_authorization()
    {
        $this->creditCardProvider()->willApprove();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/reverse-authorization');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertRedirectResponse('/merchant');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_reverse_authorization_when_reverse_was_declined()
    {
        $this->creditCardProvider()->willDecline();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/reverse-authorization');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test */
    public function it_can_refund_captured()
    {
        $this->creditCardProvider()->willApprove();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withCaptured(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/refund-captured');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertRedirectResponse('/merchant');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_refund_captured_when_refund_was_declined()
    {
        $this->creditCardProvider()->willDecline();

        $this->buildPersisted(
            MerchantBuilder::create()
                ->authorizedTo(Money::GBP(100))
                ->withCaptured(Money::GBP(100))
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_MERCHANT');
        $this->request('GET', '/refund-captured');

        $this->fillAndSubmitForm('funds[save]', ['funds[amount]' => '100']);

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertThatFormIsNotValid();
    }

    /** @test @dataProvider wrongRoles */
    public function user_with_wrong_role_can_not_access_merchant_controller(string $uri, string $role)
    {
        $this->authenticateWithRole($role);

        $this->request('GET', $uri);

        $this->assertResponseStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function wrongRoles(): array
    {
        return [
            ['/merchant', 'ROLE_CUSTOMER'],
            ['/capture-authorization', 'ROLE_CUSTOMER'],
            ['/reverse-authorization', 'ROLE_CUSTOMER'],
            ['/refund-captured', 'ROLE_CUSTOMER'],
        ];
    }
}
