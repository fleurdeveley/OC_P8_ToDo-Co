<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Tests\AbstractWebTestCase;
use App\Tests\Traits\Login;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends AbstractWebTestCase
{
    use Login;

    public function testLoginPageStatusCode()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDisplayLogin()
    {
        $this->client->request('GET', '/login');

        $this->assertSelectorTextContains('h1', 'Connectes toi');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'fakeusername',
            'password' => 'fakepassword'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/login",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connectes toi.');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfullLogin()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'Admin',
            'password' => 'password'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSuccessfullLogout()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);

        $this->login($this->client, $user);

        $this->client->request('GET', '/logout');

        // $urlGenerator = static::getContainer()->get(UrlGenerator::class);
        // $url = $urlGenerator->generate('app_login');

        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
    }
}
