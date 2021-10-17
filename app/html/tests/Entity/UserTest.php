<?php

namespace App\Entity;

use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;
use App\Tests\Traits\Login;
use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends AbstractWebTestCase
{
    use Login;

    protected $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testUserTasks()
    {
        $user = new User();
        $task = new Task();

        $user = $user->addTask($task);
        $tasks = $user->getTasks();
        $this->assertInstanceOf(ArrayCollection::class, $tasks);
        $this->assertNotEmpty($tasks);

        $this->assertInstanceOf(Task::class, $tasks->first());

        $user = $user->removeTask($tasks->first());
        $tasks = $user->getTasks();
        $this->assertEmpty($tasks);
    }
}