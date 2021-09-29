<?php

namespace App\Tests\Controller;

use App\Tests\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
