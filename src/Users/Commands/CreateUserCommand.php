<?php

namespace GeekBrains\LevelTwo\Users\Commands;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\CommandException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;


class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $userRep,
        private LoggerInterface $logger // добавим зависимость
    )
    {        
    }

    public function handle(Arguments $arrArgv): void
    {
        // Логируем информацию о том, что команда запущена. Уровень – INFO
        $this->logger->info("Create user command started");

        $login = $arrArgv->get('login');
             

        if ($this->userExists($login)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $login");
            throw new CommandException("User already exists: $login");
        }

        $user = User::createFrom(
            $login, 
            $arrArgv->get('user_name'), 
            $arrArgv->get('user_surname'), 
            $arrArgv->get('password')
        );

        $this->userRep->save($user);
           
        $this->logger->info("User created:" .  $user->getLogin());    
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