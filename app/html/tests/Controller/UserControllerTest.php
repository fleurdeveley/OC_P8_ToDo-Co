<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Tests\AbstractWebTestCase;
use App\Tests\Traits\Login;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends AbstractWebTestCase
{
    use Login;

    public function testUserListLoginAdmin()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateUser()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Username',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'username@gmail.com'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/login",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditUserWhenAuthenticatedAsAdmin()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/'. $user->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'Username10',
            'user[password][first]' => 'password06',
            'user[password][second]' => 'password06',
            'user[email]' => 'username10@gmail.com'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/users",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteUserAsAdmin()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);
        $userDelete = $this->userRepository->find(2);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/'. $userDelete->getId() . '/delete');

        $this->assertResponseRedirects(
            "/users",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $userDelete = $this->userRepository->find(2);
        $this->assertNull($userDelete);
    }
}