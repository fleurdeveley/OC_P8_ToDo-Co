<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $faker;
    protected $hasher;
    protected $users = [];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 3; $i++) {
            $user = new User();

            $user->setUsername($this->faker->name())
                ->setEmail("username$i@gmail.com")
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);

            $this->users[] = $user;
        }

        // 10 tasks
        for($i = 1; $i <= 10; $i++) {
            $task = new Task;

            $task->setCreatedAt(new DateTime())
                ->setTitle("Tâche $i")
                ->setContent("Le contenu de ma tâche $i")
                ->toggle(mt_rand(0, 1))
                ->setUser($this->faker->randomElement($this->users));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
