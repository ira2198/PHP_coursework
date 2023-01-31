<?php

namespace GeekBrains\LevelTwo\Users\Commands;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\CommandException;



class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $userRep
    )
    {        
    }

    public function handle(array $arrArgv): void
    {
        $input = $this-> parseRawInput($arrArgv);
        $login = $input['login'];

        if ($this->userExists($login)) {
            throw new CommandException("User already exists: $login");
        }

        $this->userRep->save(new User(
            UUID::random(), 
            $login,
            $input['user_name'], 
            $input['user_surname']
        ));        
    }

    private function parseRawInput(array $arrArgv): array
    {
        $input = [];

        foreach ($arrArgv as $argument) {
            $parts = explode('=', $argument);
            if (count($parts) !== 2) {
                continue;
            }        
            $input[$parts[0]] = $parts[1];
        }

        foreach (['login', 'user_name', 'user_surname'] as $argument) {
            if (!array_key_exists($argument, $input)) {
                throw new CommandException(
                    "No required argument provided: $argument"
                );
            }
            if (empty($input[$argument])) {
                throw new CommandException(
                    "Empty argument provided: $argument"
                );
            }
        }
    return $input;
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



