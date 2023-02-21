<?php

namespace GeekBrains\LevelTwo\Http\Actions\likes;

use GeekBrains\LevelTwo\Blog\Likes\LikeComment;
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\LikeAlreadyExists;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\CommentLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class CreateLikeComment implements ActionsInterface
{
    public function __construct(
        private TokenAuthIdenticationInterface $identification,
        private CommentsRepositoryInterface $commentsRepository,
        private CommentLikeRepoInterface $likeRepository,
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


        try {
            $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $this->likeRepository->checkUserLikeForCommentExists($user->getUuid(), $commentUuid);
        } catch (LikeAlreadyExists $err) {
            return new ErrResponse($err->getMessage());
        }


        try {
            $comment = $this->commentsRepository->get($commentUuid);
        } catch (PostNotFoundException $err) {
            return new ErrResponse($err->getMessage());
        }

        $newLikeCommentUuid = UUID::random();

        try {
            $like = new LikeComment(
                $newLikeCommentUuid,
                $user,
                $comment,
            );
        } catch (HttpException $err) {
                return new ErrResponse($err->getMessage());
        }

        $this->likeRepository->save($like);
        
        $this->logger->info("like the comment: $newLikeCommentUuid");
        return new SuccessFullResponse([
            'uuid' => (string)$newLikeCommentUuid
            ]);
    }  

}