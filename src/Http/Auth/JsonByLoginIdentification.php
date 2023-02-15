<?php

namespace GeekBrains\LevelTwo\Http\Auth;

use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use InvalidArgumentException;

class JsonByLoginIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {        
    }

    public function author(Request $request): User
    {
          // Cоздаем  UUID пользователя из данных запроса
          try {
            $author = $request->jsonBodyField('login');
        } catch (HttpException | InvalidArgumentException $err) {
            return new AuthExceptions($err->getMessage());
        }

        // Ищем пользователя в репозитории
         try {
            return $this->usersRepository->getByUserLogin($author);
        } catch (UserNotFoundExceptions $err) {
           return new AuthExceptions($err->getMessage());
        }
    }    
}