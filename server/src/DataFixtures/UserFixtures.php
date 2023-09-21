<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    public const ROLE_USER = ['ROLE_USER'];
    public const ROLE_ADMIN = ['ROLE_ADMIN'];
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Создание администратора.
        $admin = new User();
        $admin
            ->setLogin("admin")
            ->setPassword($this->passwordHasher->hashPassword($admin, "admin"))
            ->setFio($this->faker->name)
            ->setEmail($this->faker->email)
            ->setRoles(self::ROLE_ADMIN);
        $manager->persist($admin);
        $this->saveReference($admin);

        // Создание пользователей
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user
                ->setLogin("login$i")
                ->setPassword($this->passwordHasher->hashPassword($user, "password$i"))
                ->setFio($this->faker->name)
                ->setEmail($this->faker->email)
                ->setRoles(self::ROLE_USER);
            $manager->persist($user);
            $this->saveReference($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }

}