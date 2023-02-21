<?php

namespace GeekBrains\LevelTwo\Http\Actions\Users;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Users\User;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionsInterface
{
    public function __construct(
        private UsersRepositoryInterface $userRepository,
        private LoggerInterface $logger
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $user = User::createFrom(
                $request->jsonBodyField('login'),
                $request->jsonBodyField('user_name'),
                $request->jsonBodyField('user_surname'),
                $request->jsonBodyField('password')
            );
        } catch (HttpException $err) {
            return new ErrResponse($err->getMessage());
        }

        $this->userRepository->save($user);

        return new SuccessFullResponse([
            'Создан пользователь' => $request->jsonBodyField('login')
        ]);

        $this->logger->info("User: {$request->jsonBodyField('login')} created in CreateUser class");
    }
}