<?php
namespace GeekBrains\UnitTests\Commands\UsersCommands;


use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Commands\UsersCommands\CreateUser;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserTest extends TestCase
{

   // заглушка
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


    public function testItRequiresLastNameSymf(): void
    {
        // Тестируем новую команду
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        // Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
        // .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "user_surname").'
        );

        // Запускаем команду методом run вместо handle
        $command->run(
            //  ArrayInput симмулирует консольный ввод
            // Сами аргументы не меняются
            new ArrayInput([
                'login' => 'Ivan',            
                'user_name' => 'Ivan',
                'password' => '111'
            ]),
            // симмулирует вывод OutputInterface
            // Нам подойдёт реализация, которая ничего не делает
            new NullOutput()
        );

    }


    // требуется ли пароль?
    public function testItRequiresPasswordSymf(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "user_name, user_surname, password")'
        );

        $command->run(
                new ArrayInput([
                'login' => 'Ivan',
            ]),
            
            new NullOutput()
        );
    }


    public function testItRequiresUserNameSymf(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "user_name, user_surname").'
        );
        $command->run(
            new ArrayInput([
                'login' => 'Ivan',
                'password' => '111'
            ]),
            new NullOutput()
            );
    }

    // Сщхраняет ли в репозиторий

    public function testItSavesUserToRepositorySymf(): void
    {
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
        
        $command = new CreateUser(
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'login' => 'Ivan',
                'user_name' => 'Ivan',
                'user_surname' => 'Nikitin',
                'password' => '111'
            ]),
        new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled()); 
    }
}  