<?php

namespace GeekBrains\LevelTwo\Http\Actions\AuthAction;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\BearerTokenAuthentication;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\AuthTokenRepoInterface;

class LogOut implements ActionsInterface 
{
    public function __construct(
        private AuthTokenRepoInterface $tokenRepository,
        private BearerTokenAuthentication $authentication
    )
    {        
    }

    public function handle( Request $requesrt): Response
    {
        $token = $this->authentication->getAuthToken($requesrt);

        try {
            $authToken = $this->tokenRepository->getToken($token);
        } catch (AuthTokenNotFoundException $err) {
            throw new AuthExceptions($err->getMessage());
        }

        $authToken->setExpiresOn(new \DateTimeImmutable('+1 day'));

        $this->tokenRepository->saveToken($authToken);

        return new SuccessFullResponse([
            'token' => $authToken->getToken()
        ]);
    }
}