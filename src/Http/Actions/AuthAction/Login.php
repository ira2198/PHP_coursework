<?php

namespace GeekBrains\LevelTwo\Http\Actions\AuthAction;

use DateTimeImmutable;
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\AuthToken;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\AuthTokenRepoInterface;

class Login implements ActionsInterface 
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passAuthentication,
        // через токены
        private AuthTokenRepoInterface $tokensRepository
    ) {        
    }

    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
         try {
            $user = $this->passAuthentication->author($request);
        } catch (AuthExceptions $err) {
            return new ErrResponse($err->getMessage());
        }

        // Генерируем токен
        $authToken = new AuthToken(
            bin2hex(random_bytes(40)), //случайный набор из 40 символов
            $user->getUuid(),       
            (new DateTimeImmutable())->modify('+1 day') // Срок годности - 1 день
        );

            // Сохраняем токен в репозиторий
        $this->tokensRepository->saveToken($authToken);
            // Возвращаем токен
        return new SuccessFullResponse([
            'token' => (string)$authToken->getToken(),
            ]);
    }      
    
}