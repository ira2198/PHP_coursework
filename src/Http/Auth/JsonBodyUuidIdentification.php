<?php

namespace GeekBrains\LevelTwo\Http\Auth;


use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use InvalidArgumentException;

class JsonBodyUuidIdentification implements IdentificationInterface 
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
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new AuthExceptions($err->getMessage());
        }

        // Ищем пользователя в репозитории
         try {
            return $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundExceptions $err) {
            return new AuthExceptions($err->getMessage());
        }
    }
}


