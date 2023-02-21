<?php

namespace GeekBrains\LevelTwo\Http\Auth;

use DateTimeImmutable;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\AuthTokenRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\User;

//Читаем приходящие токены 

class BearerTokenAuthentication implements TokenAuthIdenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';  //отсекает Bearer от запроса 

    public function __construct(
    // Репозиторий токенов
    private AuthTokenRepoInterface $tokensRepository,
    // Репозиторий пользователей
    private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function author(Request $request): User
    {
             
        $token = $this->getAuthToken($request);

        // Ищем токен в репозитории
        try {
        $authToken = $this->tokensRepository->getToken($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthExceptions("Bad token: [$token]");
        }

        // Проверяем срок годности токена
        if ($authToken->getExpiresOn() <= new DateTimeImmutable()) {
            throw new AuthExceptions("Token expired: [$token]");
        }

        // Получаем UUID пользователя из токена
        $userUuid = $authToken->getUserUuid();
        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }
    

    public function getAuthToken(Request $request): string
    {

         // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $err) {
            throw new AuthExceptions($err->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthExceptions("Malformed token: [$header]");
        }

        return mb_substr($header, strlen(self::HEADER_PREFIX));  // Отрезаем префикс Bearer
    }
}