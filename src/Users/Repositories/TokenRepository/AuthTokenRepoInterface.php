<?php

namespace GeekBrains\LevelTwo\Users\Repositories\TokenRepository;

interface AuthTokenRepoInterface 
{
    public function saveToken(AuthToken $authToken): void;

    public function getToken(string $token): AuthToken;
    
}