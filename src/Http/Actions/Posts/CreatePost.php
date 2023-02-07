<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;



class CreatePost implements ActionsInterface
{
    
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $userRepository
        ) 
    {
    }        

    public function handle(Request $request): Response
    {
        // Cоздаем  UUID пользователя из данных запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }

        // Ищем пользователя в репозитории
        try {
            $user = $this->userRepository->get($authorUuid);
        } catch (UserNotFoundExceptions $err) {
            return new ErrResponse($err->getMessage());
        }

        // Генерируем UUID для новой статьи
        $newPostUuid = UUID::random();

        try {
        // Пытаемся создать объект статьи из данных запроса
            $post = new Post(
            $newPostUuid,
            $user,
            $request->jsonBodyField('title'),
            $request->jsonBodyField('content'),
        );
        } catch (HttpException $err) {
            return new ErrResponse($err->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        // Возвращаем успешный ответ, содержащий UUID новой статьи
        return new SuccessFullResponse([
        'uuid' => $request->jsonBodyField('title'),
        ]);
    }

}