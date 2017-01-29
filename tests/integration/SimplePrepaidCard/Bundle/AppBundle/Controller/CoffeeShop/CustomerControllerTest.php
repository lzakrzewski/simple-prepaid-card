<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CoffeeShop;

use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use Symfony\Component\HttpFoundation\Response;
use tests\builders\CoffeeShop\CustomerBuilder;
use tests\builders\CoffeeShop\MerchantBuilder;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_render_customer_page()
    {
        $this->authenticateWithRole('ROLE_CUSTOMER');
        $this->request('GET', '/customer');

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function it_can_buy_a_product()
    {
        $this->creditCardProvider()->willApprove();

        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId(Uuid::fromString(Customer::CUSTOMER_ID))
        );

        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_CUSTOMER');
        $this->request('GET', '/buy-product');

        $this->fillAndSubmitForm('product[submit]', []);

        $this->assertRedirectResponse('/customer');
        $this->assertThatFormIsValid();
    }

    /** @test */
    public function it_can_not_buy_a_product_when_authorization_request_was_declined()
    {
        $this->creditCardProvider()->willDecline();

        $this->buildPersisted(
            CustomerBuilder::create()
                ->withCustomerId(Uuid::fromString('5a29e675-1c05-4323-ae72-9ffbbb17ad38'))
        );

        $this->buildPersisted(
            MerchantBuilder::create()
                ->withMerchantId(Uuid::fromString(Merchant::MERCHANT_ID))
        );

        $this->authenticateWithRole('ROLE_CUSTOMER');
        $this->request('GET', '/buy-product');

        $this->fillAndSubmitForm('product[submit]', []);

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
            ['/customer', 'ROLE_MERCHANT'],
            ['/buy-product', 'ROLE_MERCHANT'],
        ];
    }
}
