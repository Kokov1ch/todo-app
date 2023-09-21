<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends BaseFixture implements DependentFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->getReferencesByEntityClass(User::class);
        for ($i = 0; $i < 10; ++$i) {
            $user = $this->faker->randomElement($users);
            $wordsArray = $this->faker->unique()->words(2);
            $name = implode(' ', $wordsArray);
            $startDate = $this->faker->dateTimeThisMonth;
            $endDate = $this->faker->dateTimeBetween($startDate, '+10 days');

            $task = new Task();
            $task
                ->setName($name)
                ->setDescription($this->faker->realTextBetween(10, 60, 5), '.')
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setDone(false)
                ->setUser($user);

            $manager->persist($task);
            $this->saveReference($task);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}