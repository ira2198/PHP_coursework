<?php

namespace GeekBrains\LevelTwo\Users;


class User 
{
    public function __construct(
        private int $id,
        private string $userName,
        private string $userSurname
    )
    {        
    }

    public function  __toString() 
    {
        return $this->id . ' - ' . $this->userName . ' ' . $this->userSurname . PHP_EOL;
    }  


    
    public function getId (): int
    {
        return $this->id;
    }
    public function getUserName (): string
    {
        return $this->userName;
    }
    public function getUserSurname (): string
    {
        return $this->userSurname;
    }


    public function setUserName (string $userName): void
    {
        $this->userName = $userName;
    }
    public function setUserSurname ( string $userSurname): void
    {
        $this->userSurname= $userSurname;
    }

}
