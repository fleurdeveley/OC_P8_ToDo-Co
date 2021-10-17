<?php

namespace App\Listener;

use App\Tests\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListenerTest extends AbstractWebTestCase
{
    public function testOnKernelException()
    {
        $this->client->request('GET', '/tests');

        $this->assertSelectorTextContains('h1', 'Oups, une erreur est survenue');
    }
}