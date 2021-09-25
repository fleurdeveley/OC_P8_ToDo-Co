<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;
use App\Tests\Traits\Login;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends AbstractWebTestCase
{
    use Login;

    protected $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testTaskList()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testTaskListLogin()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        // Retrieve the session of the user with id = 1
        $user = $this->userRepository->find(1);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskWithConnectedUser()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Nouvelle tâche',
            'task[content]' => 'Description nouvelle tâche'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $task = $this->taskRepository->findLastTask();
        $this->assertEquals('Nouvelle tâche', $task->getTitle());
        $this->assertEquals('Description nouvelle tâche', $task->getContent());
        $this->assertEquals(1, $task->getUser()->getId());
    }

    public function testCreateTaskWithoutConnectedUser()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Nouvelle tâche',
            'task[content]' => 'Description nouvelle tâche'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $task = $this->taskRepository->findLastTask();
        $this->assertEquals('Nouvelle tâche', $task->getTitle());
        $this->assertEquals('Description nouvelle tâche', $task->getContent());
        $this->assertNull($task->getUser());
    }

    public function testDeleteTask()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $lastTask = $this->taskRepository->findLastTask();
        $id = $lastTask->getId();

        if($lastTask->getUser()) {
            $user = $this->userRepository->find($lastTask->getUser()->getId());
        } else {
            $user = $this->userRepository->find(1);
        }
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/' . $lastTask->getId() . '/delete');

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $task = $this->taskRepository->find($id);
        $this->assertNull($task);
    }

    public function testToggleTask()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $lastTask = $this->taskRepository->findLastTask();
        $id = $lastTask->getId();
        $isDone = $lastTask->getIsDone();

        if($lastTask->getUser()) {
            $user = $this->userRepository->find($lastTask->getUser()->getId());
        } else {
            $user = $this->userRepository->find(1);
        }
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/' . $lastTask->getId() . '/toggle');

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $task = $this->taskRepository->find($id);
        $this->assertTrue($task->getIsDone() != $isDone);
    }

    public function testEditTaskWithConnectedUser()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $lastTask = $this->taskRepository->findLastTask();
        $id = $lastTask->getId();

        if($lastTask->getUser()) {
            $user = $this->userRepository->find($lastTask->getUser()->getId());
        } else {
            $user = $this->userRepository->find(1);
        }
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/' . $lastTask->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Modification de la nouvelle tâche',
            'task[content]' => 'Modification de la description nouvelle tâche'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects(
            "/tasks",
            Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $task = $this->taskRepository->find($id);
        $this->assertEquals('Modification de la nouvelle tâche', $task->getTitle());
        $this->assertEquals('Modification de la description nouvelle tâche', $task->getContent());
    }
}
