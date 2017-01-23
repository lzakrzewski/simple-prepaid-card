<?php

declare(strict_types=1);

namespace tests\integration\SimplePrepaidCard\Bundle\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->request('GET', '/');

        $this->assertResponseStatusCode(Response::HTTP_OK);
    }
}
