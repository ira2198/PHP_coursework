<?php
namespace GeekBrains\LevelTwo\Users\Repositories\UsersRepositories;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;


class InMemoryUsersRep implements UsersRepositoryInterface
{
    private array $users = [];


    public function save(User $user): void 
    {
        $this->users[] = $user;
    }


    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user){
            if ($user->id() === $uuid) {
                return $user;
            }
      
        }
        throw new UserNotFoundExceptions("User not Found: $uuid");
    }   
    
    public function getByUserLogin(string $login): User
    {
       
    }
}


