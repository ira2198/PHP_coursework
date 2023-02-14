<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionsInterface
{
    
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private IdentificationInterface $identification,

        private LoggerInterface $logger
        ) 
    {
    }        

    public function handle(Request $request): Response
    {       
        try {
        $user = $this->identification->author($request);
        } catch (AuthExceptions $err) {
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

        $this->logger->info("The post: $newPostUuid was created successfully in CreatePost");

        // Возвращаем успешный ответ, содержащий UUID новой статьи
        return new SuccessFullResponse([
        'title' => $request->jsonBodyField('title'),
        ]);
    }

}