<?php
namespace GeekBrains\LevelTwo\Users\Repositories\UsersRepositories;

use GeekBrains\LevelTwo\Users\{UUID, User};
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use PDO;
use PDOStatement;


class SqliteUsersRep implements UsersRepositoryInterface
{ 
       public function __construct(
        private PDO $connectDB
    ) {        
    }

    public function save( User $user): void 
    {
        $statement = $this->connectDB->prepare(
            "INSERT INTO users (uuid, login, user_name, user_surname) VALUES (:uuid, :login, :user_name, :user_surname)"
        );   
        $statement->execute([
            ':uuid' => $user -> getUuid(),
            ':login' => $user-> getLogin(),
            ':user_name' => $user->getUserName(),
            ':user_surname' => $user->getUserSurname()      
        ]);    
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connectDB->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([(string)$uuid]);

        return $this->getUser($statement, $uuid);
    }



    public function getByUserLogin(string $login): User
    {
        $statement = $this->connectDB->prepare(
            'SELECT * FROM users WHERE login = :login'
        );
        $statement->execute([
            ':login' => $login,
        ]);

       return $this->getUser($statement, $login);
    }


    private function getUser(PDOStatement  $statement, string $errorString): User
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundExceptions(
                "Cannot find user: $errorString"
            );
        }
        return new User(
            new UUID($result['uuid']), 
            $result['login'],
            $result['user_name'], 
            $result['user_surname']);
    }
}