<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Symfony\Component\HttpFoundation\Response;
use tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller\WebTestCase;

//Todo: Missing test case for non-empty
class StatementControllerTest extends WebTestCase
{
    /** @test */
    public function it_can_get_statement()
    {
        $this->request('GET', '/statement');

        $this->assertResponseStatusCode(Response::HTTP_OK);
        $this->assertContains('<table', $this->responseContent());
    }
}
