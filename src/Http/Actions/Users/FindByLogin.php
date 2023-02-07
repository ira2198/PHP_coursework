<?php

namespace GeekBrains\LevelTwo\Http\Actions\Users;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;


class FindByLogin implements ActionsInterface
{
    // внедряем репозит. пользователей в качестве зависимости

    public function __construct(
        private UsersRepositoryInterface $userRepository
        )
    {        
    }

    public function handle(Request $request): Response
    {
        try{
            $login = $request->query('login');
        } catch(HttpException $err) {
            return new ErrResponse($err->getMessage());

        }

        try{
            $user = $this->userRepository->getByUserLogin($login);
        } catch( UserNotFoundExceptions $err) {

            return new ErrResponse($err->getMessage());
        }

        // Возвращаем успешный ответ
    return new SuccessFullResponse([
        'login' => $user->getLogin(),
        'name' => $user->getUserName() . ' ' . $user->getUserSurname(),
        ]);    
    }
}
