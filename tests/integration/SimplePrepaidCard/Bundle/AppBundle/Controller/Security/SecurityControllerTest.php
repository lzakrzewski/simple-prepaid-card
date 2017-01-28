<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\Security;

use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /** @test */
    public function it_returns_login_page_when_user_is_not_authenticated()
    {
        $this->request('GET', '/');

        $this->assertRedirectResponse('http://localhost/login');
    }

    /** @test */
    public function it_can_authenticate_customer_with_valid_credentials()
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('login', ['_username' => 'customer', '_password' => 'customer']);

        $this->followRedirect();
        $this->assertRedirectResponse('/customer');
        $this->assertUsername('customer');
    }

    /** @test */
    public function it_can_authenticate_merchant_with_valid_credentials()
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('login', ['_username' => 'merchant', '_password' => 'merchant']);

        $this->followRedirect();
        $this->assertRedirectResponse('/merchant');
        $this->assertUsername('merchant');
    }

    /** @test */
    public function it_can_not_authenticate_customer_with_valid_credentials()
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('login', ['_username' => 'customer', '_password' => 'invalid']);

        $this->assertRedirectResponse('http://localhost/login');
        $this->assertUsername('');
    }

    /** @test */
    public function it_can_not_authenticate_merchant_with_valid_credentials()
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('login', ['_username' => 'merchant', '_password' => 'invalid']);

        $this->assertRedirectResponse('http://localhost/login');
        $this->assertUsername('');
    }

    /** @test */
    public function it_can_log_out()
    {
        $this->request('GET', '/login');

        $this->fillAndSubmitForm('login', ['_username' => 'merchant', '_password' => 'merchant']);

        $this->request('GET', '/logout');

        $this->followRedirect();
        $this->assertRedirectResponse('http://localhost/login');
        $this->assertUsername('anon.');
    }
}
