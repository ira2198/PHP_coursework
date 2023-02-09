<?php

namespace GeekBrains\UnitTests\Container;

use GeekBrains\LevelTwo\Container\DIContainer;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\InMemoryUsersRep;
use GeekBrains\LevelTwo\Copntainer\SomeClassWithoutDependencies;
use GeekBrains\LevelTwo\Users\Exceptions\ContainerNotFoundException;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = new DIContainer();
        // ожидаемые исключения
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Cannot resolve type: GeekBrains\UnitTests\tests\SomeClass');

        // Пытаемся получить обЪект несуществующего класса
        $container->get(SomeClass::class);


    }


    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object 
        );
    }

    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Устанавливаем правило, по которому всякий раз, когда контейнеру нужно создать объект, реализующий контракт
        // UsersRepositoryInterface, он возвращал бы объект класса InMemoryUsersRep
        $container->bind(
        UsersRepositoryInterface::class,
        InMemoryUsersRep::class
        );

        // Пытаемся получить объект класса, реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);

        // Проверяем, что контейнер вернул объект класса InMemoryUsersRep
        $this->assertInstanceOf(
        InMemoryUsersRep::class,
        $object
        );

    }

    public function testItReturnsPredefinedObject(): void
    {   
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому всякий раз, когда контейнеру нужно
        // вернуть объект типа SomeClassWithParameter, он возвращал бы предопределённый объект
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        // Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);

        // Проверяем, что контейнер вернул объект того же типа
        $this->assertInstanceOf(
        SomeClassWithParameter::class,
        $object
        );

        // Проверяем, что контейнер вернул тот же самый объект
        $this->assertSame(42, $object->value());
        }

    public function testItResolvesClassWithDependencies(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило получения объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);

        // Проверяем, что контейнер вернул объект нужного нам типа
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }

}