<?php

namespace GeekBrains\LevelTwo\Http\Auth;

use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    
    public function __construct(
        private UsersRepositoryInterface $userRepository
    )
    {        
    }

    public function author(Request $request): User
    {
        // идентификация
        try {
            $login = $request->jsonBodyField('login');
        } catch (HttpException $err) {
            throw new AuthExceptions($err->getMessage());
        }
        try {
            $user = $this->userRepository->getByUserLogin($login);
        } catch (UserNotFoundExceptions $err) {
            throw new AuthExceptions($err->getMessage());
        }

        // 2. Аутентифицируем пользователя
        // Проверяем, что пароль соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $err) {
            throw new AuthExceptions($err->getMessage());
        }
       
        if (!$user->checkPassword($password)) {
            throw new AuthExceptions('Wrong password');
        }
        // Пользователь аутентифицирован
        return $user;    
    }
}

