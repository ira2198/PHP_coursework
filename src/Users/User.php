<?php

namespace GeekBrains\LevelTwo\Users;


class User 
{
    public function __construct(
        private UUID $uuid,
        private string $login,
        private string $userName,
        private string $userSurname,
        private string $password // сделаем хэширование прямо тут 
    )
    {        
    }

     //хэшируем получаемый пароль
    public static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password); 
    }

    //сюда же проверку 
    public function checkPassword(string $newPassword):bool
    {
        return $this->password === self::hash($newPassword, $this->uuid);
    }
    
    //и будем здесь пересобирать нового пользователя

    public static function createFrom(
        string $login, string $userName, string $userSurname, string $password
        ): self
    {
        $uuid = UUID::random();
        //создаем экземпляр self - себя
        return new self(
            $uuid, 
            $login,
            $userName, 
            $userSurname, 
            self::hash($password, $uuid)
        );
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

    public function getPassword(): string
    {
        return $this->password; 
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
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
