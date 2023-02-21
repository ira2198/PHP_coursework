<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\UUID;
use Psr\Log\LoggerInterface;

class CreateComments implements ActionsInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $userRepository,
        private TokenAuthIdenticationInterface $identification,
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

         // Cоздаем  UUID статьи из данных запроса        
       
        try {
            $postUuid = new UUID($request->jsonBodyField('article_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }


        // Ищем статью в репозитории
       
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $err) {
            return new ErrResponse($err->getMessage());
        }

        // Генерируем UUID для комментария
        $newCommentUuid = UUID::random();

        try {
            // Создаем объект коммент из данных запроса
                $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $err) {
                return new ErrResponse($err->getMessage());
        }

         // Сохраняем 
        $this->commentsRepository->save($comment);

        $this->logger->info("comment: $newCommentUuid saved successfully in CreateComments class");
        // Возвращаем успешный ответ
        return new SuccessFullResponse([
            'text' => $request->jsonBodyField('text'),
            ]);
       
    }

}
