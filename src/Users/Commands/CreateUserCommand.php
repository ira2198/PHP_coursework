<?php

namespace GeekBrains\LevelTwo\Users\Commands;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Users\Exceptions\CommandException;



class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $userRep
    )
    {        
    }

    public function handle(Arguments $arrArgv): void
    {
        $login = $arrArgv->get('login');

        if ($this->userExists($login)) {
            throw new CommandException("User already exists: $login");
        }

        $this->userRep->save(new User(
            UUID::random(), 
            $login,
            $arrArgv->get('user_name'), 
            $arrArgv->get('user_surname')
        ));        
    }



private function userExists(string $login): bool
{
    try {
// Пытаемся получить пользователя из репозитория
        $this->userRep->getByUserLogin($login);
    } catch (UserNotFoundExceptions) {
        return false;
    }
    return true;
    }
}



