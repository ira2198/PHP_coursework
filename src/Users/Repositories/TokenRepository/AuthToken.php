<?php

namespace GeekBrains\LevelTwo\Users\Repositories\TokenRepository;


use DateTimeImmutable;
use GeekBrains\LevelTwo\Users\UUID;

class AuthToken 
{
    public function __construct(
        private string $token,
        private UUID $userUuid,
        private DateTimeImmutable $expires
    )
    {}
    
    public function getToken(): string
    {
        return $this->token;
    }
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }
    public function getExpiresOn(): DateTimeImmutable
    {
        return $this->expires;
    }  
    
    
    public function setExpiresOn($currentDate): void
    {
        $this->expires = $currentDate;
    }
  
}