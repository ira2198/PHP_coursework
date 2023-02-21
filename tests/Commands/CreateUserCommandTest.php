<?php
namespace GeekBrains\UnitTests\Commands;


use GeekBrains\LevelTwo\Users\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Users\Commands\Arguments;
use GeekBrains\LevelTwo\Users\Exceptions\CommandException;
use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Users\Repositories\DummyUsersRepository;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\UnitTests\DummyLogger;

use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{

// Тест на исключения
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger);
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);

        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');

        // Запускаем команду с аргументами
        $command->handle(new Arguments(['login' => 'Ivan', 'password' => '123']));
    }


    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');

        $command->handle(new Arguments([
            'login' => 'Ivan',
            'user_name' => 'Ivan',
            'user_surname' => 'Nikitin'            
           ]));
    }


    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
        //- объект анонимного класса, реализующего контракт UsersRepositoryInterface
        $usersRepository = new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
               throw new UserNotFoundExceptions("Not found");
            }

            public function getByUserLogin(string $login): User
            {
                throw new UserNotFoundExceptions("Not found");
            }
        };

    // Передаём объект анонимного класса  в качестве реализации UsersRepositoryInterface    
        $command = new CreateUserCommand($usersRepository, new DummyLogger);

    // Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: user_name');
    // Запускаем команду
        $command->handle(new Arguments(['login' => 'Ivan', 'password' => '123']));
    }


// Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresSurname(): void
      {
  // Передаём в конструктор команды объект, возвращаемый нашей функцией
          $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger);
          $this->expectException(ArgumentsException::class);
          $this->expectExceptionMessage('No such argument: user_surname');
          $command->handle(new Arguments([
              'login' => 'Ivan',
   // Нам нужно передать имя пользователя, чтобы дойти до проверки наличия фамилии
              'user_name' => 'Ivan',
              'password' => '123'
          ]));
      }
   
    // Функция возвращает объект типа UsersRepositoryInterface
      private function makeUsersRepository(): UsersRepositoryInterface
      {
          return new class implements UsersRepositoryInterface {
              public function save(User $user): void
              {
              }  
              public function get(UUID $uuid): User
              {
                  throw new UserNotFoundExceptions("Not found");
              }  
              public function getByUserLogin(string $login): User  
              {
                  throw new UserNotFoundExceptions("Not found");
              }
          };
      }


    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {

    // В этом свойстве мы храним информацию о том, был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
    // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {

                throw new UserNotFoundExceptions("Not found");
            }

            public function getByUserLogin(string $login): User
            {
                throw new UserNotFoundExceptions("Not found");
            }

// С помощью этого метода мы можем узнать, был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository, new DummyLogger);

        // Запускаем команду
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'user_name' => 'Ivan',
            'user_surname' => 'Nikitin',
            'password' => '123'
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }  
}
        