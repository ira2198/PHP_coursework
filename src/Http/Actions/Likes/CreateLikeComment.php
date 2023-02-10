<?php

namespace GeekBrains\LevelTwo\Http\Actions\likes;

use GeekBrains\LevelTwo\Blog\Likes\LikeComment;
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\LikeAlreadyExists;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\CommentLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use InvalidArgumentException;

class CreateLikeComment implements ActionsInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private CommentLikeRepoInterface $likeRepository
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $this->likeRepository->checkUserLikeForCommentExists($authorUuid, $commentUuid);
        } catch (LikeAlreadyExists $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundExceptions $err) {
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

        return new SuccessFullResponse([
            'uuid' => (string)$newLikeCommentUuid
            ]);
    }  

}