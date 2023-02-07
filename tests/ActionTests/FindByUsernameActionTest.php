<?php

namespace tests\ActionTests;

use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\http\Actions\Users\FindByLogin;
use GeekBrains\LevelTwo\http\ErrResponse;
use GeekBrains\LevelTwo\http\SuccessFullResponse;
use PHPUnit\Framework\TestCase;


class FindByUsernameActionTest extends TestCase
{

    // Запускаем тест в отдельном процессе
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */

    public function testItReturnsErrorResponseIfNoLoginProvided(): void
    {
        $request =  new Request([], [], ""); // простые массивы вместо суперглобальных

        //стаб репозитория пользователей
        $usersRepository = $this->usersRepository([]);
        

        //Создаём объект действия
        $action = new FindByLogin($usersRepository);

        // Запускаем действие
        $response = $action->handle($request);

        // Проверяем, что ответ - неудачный
        $this->assertInstanceOf(ErrResponse::class, $response);
        // Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: login"}');
      
        // Отправляем ответ в поток вывода
        $response->send();
    }


        /**
        * @runInSeparateProcess
        * @preserveGlobalState disabled
        */
        // Тест, проверяющий, что будет возвращён неудачный ответ, если пользователь не найден

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        // Теперь запрос будет иметь параметр username
        $request = new Request(['login' => 'Ivan'], []);
        // Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);
        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

        /**
        * @runInSeparateProcess
        * @preserveGlobalState disabled
        */
        // Тест, проверяющий, что будет возвращён удачный ответ, если пользователь найден

    public function testItReturnsSuccessfulResponse(): void
    {

        $request = new Request(['login' => 'Ivan'], []);
        // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
            UUID::random(),
            'Ivan',
            'Ivan', 
            'Nikitin'),
        ]);


        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);

        // Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessFullResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"login":"Ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }

    // Функция, создающая стаб репозитория пользователей,
    // принимает массив "существующих" пользователей
    private function usersRepository(array $users): UsersRepositoryInterface
    {
    // В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
             )
            {
            }

        public function save(User $user): void
        {}

        public function get(UUID $uuid): User
        {
            throw new UserNotFoundExceptions("Not found");
        }
        public function getByUserLogin(string $login): User
        {
            foreach ($this->users as $user) {
            if ($user instanceof User && $login === $user->getLogin())
                {
                return $user;
                }
            }        
            throw new UserNotFoundExceptions("Not found");
        }
    };
    }   
}