<?php

namespace App\Tests;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var AbstractDatabaseTool
     */
    protected $client;
    protected $databaseTool;
    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }
}