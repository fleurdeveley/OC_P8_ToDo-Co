<?php

namespace App\Test\Repository;
use App\DataFixtures\AppFixtures;
use App\Tests\AbstractWebTestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;


class UserRepositoryTest extends AbstractWebTestCase
{
    public function testUpgradePassword()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $user = $this->userRepository->find(1);

        $this->userRepository->upgradePassword($user, 'hfgxhfghfxhgfhxfg');

        $userUpdate = $this->userRepository->find(1);

        $this->assertEquals('hfgxhfghfxhgfhxfg',  $userUpdate->getPassword());
    }
}