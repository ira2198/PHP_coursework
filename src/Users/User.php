<?php

namespace GeekBrains\LevelTwo\Users;


class User 
{
    public function __construct(
        private UUID $uuid,
        private string $login,
        private string $userName,
        private string $userSurname
    )
    {        
    }

    public function  __toString() 
    {
        return  'Пользователь ' . $this->login . "\n" . $this->uuid . ' - ' . $this->userName . ' ' . $this->userSurname . PHP_EOL;
    }  


    
    public function getUuid (): UUID
    {
        return $this->uuid;
    }
    public function getUserName (): string
    {
        return $this->userName;
    }
    public function getUserSurname (): string
    {
        return $this->userSurname;
    }
    public function getLogin ():string{
        return $this->login;
    }


    public function setUserName (string $userName): void
    {
        $this->userName = $userName;
    }
    public function setUserSurname ( string $userSurname): void
    {
        $this->userSurname = $userSurname;
    }
    public function setLogin(string $login):void
    {
        $this->login = $login;
    }

}
