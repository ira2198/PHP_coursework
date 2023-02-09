<?php

namespace GeekBrains\UnitTests\BlogTests;


use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;

use GeekBrains\LevelTwo\Users\{User, UUID};

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class SqliteUsersRepTest extends TestCase 
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {

        //coздаст мок и стаб
        $connectionMock = $this->createStub(PDO::class); 
        $statementStub = $this->createStub(PDOStatement::class);
        //задаем поведение стабу fetch --> false
        $statementStub->method('fetch')->willReturn(false);
        //prepare должен возвращать экземпряр statement
        $connectionMock->method('prepare')->willReturn($statementStub);


        $repository = new SqliteUsersRep($connectionMock);
        $this->expectException(UserNotFoundExceptions::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        $repository->getByUserLogin('Ivan');
    }
    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
    {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);

        // 5. Описываем ожидаемое взаимодействие нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':login' => 'ivan123',
                ':user_name' => 'Ivan',
                ':user_surname' => 'Nikitin',
            ]);

    // 3. При вызове метода prepare стаб подключения возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

    // 1. Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRep($connectionStub);

    // Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
    // как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'ivan123',
                'Ivan',
                'Nikitin'
                
            )
        );
    }
}