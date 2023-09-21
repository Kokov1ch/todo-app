<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture implements FixtureGroupInterface
{
    /**
     * Группа используется для __разработки__ -- данные наиболее приближены к реальным.
     */
    protected const DEV_GROUP = 'dev';
    /**
     * Группа используется для __тестирования__ -- данные не создаются случайным образом.
     */
    protected const TEST_GROUP = 'test';
    protected Generator $faker;
    private array $referenceCounter;

    public function __construct()
    {
        $this->faker = Factory::create('ru_RU');
        // Здесь можно изменить ключ генерации, чтобы получить другие фикстуры.
        $this->faker->seed(2);
        $this->referenceCounter = [];
    }

    /**
     * Находит все сущности определенного типа, на которые были созданы ссылки с помощью методов
     * addReference/setReference/saveReference при загрузке фикстур.
     *
     * @param string $entityClass Полный путь к классу сущности ($entity::class)
     * @return array Список из сущностей данного типа
     */
    protected function getReferencesByEntityClass(string $entityClass): array
    {
        $fixtures = array_filter(
            $this->referenceRepository->getReferences(),
            fn(object $entity): bool => $entity instanceof $entityClass
        );
        return array_map(
            fn(string $fixture): object => $this->getReference($fixture),
            array_keys($fixtures)
        );
    }

    /**
     * Сохраняет ссылку на объект, используя собственный протокол для наименования ссылок:
     *
     * '{entity::class}{id}'
     *
     * @param object $entity Сохраняемая БД-сущность
     * @return void
     */
    protected function saveReference(object $entity): void
    {
        if (!array_key_exists($entity::class, $this->referenceCounter)) {
            $this->referenceCounter[$entity::class] = 0;
        }

        do {
            ++$this->referenceCounter[$entity::class];
            $referenceName = $entity::class . $this->referenceCounter[$entity::class];
        } while ($this->referenceRepository->hasReference($referenceName));

        parent::setReference($referenceName, $entity);
    }
}