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

class CreateUser implements ActionsInterface
{
    public function __construct(
        private UsersRepositoryInterface $userRepository
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('login'),
                $request->jsonBodyField('user_name'),
                $request->jsonBodyField('user_surname')
            );
        } catch (HttpException $err) {
            return new ErrResponse($err->getMessage());
        }

        $this->userRepository->save($user);

        return new SuccessFullResponse([
            'uuid' => (string)$newUserUuid
        ]);
    }
}