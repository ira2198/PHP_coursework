<?php

namespace GeekBrains\LevelTwo\Http\Actions\likes;

use GeekBrains\LevelTwo\Blog\Likes\LikePost;
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Exceptions\LikeAlreadyExists;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\PostLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use InvalidArgumentException;

class CreateLikePost implements ActionsInterface 
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postRepository,
        private PostLikeRepoInterface $likeRepository
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $articleUuid = new UUID($request->jsonBodyField('article_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $this->likeRepository->checkUserLikeForPostExists($authorUuid, $articleUuid);
        } catch (LikeAlreadyExists $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundExceptions $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
          $article = $this->postRepository->get(new UUID($articleUuid));
        } catch (PostNotFoundException $err) {
            return new ErrResponse($err->getMessage());
        }

        $newLikePostUuid = UUID::random();

        try {
            $like = new LikePost(
                $newLikePostUuid,
                $user,
                $article,
            );
        } catch (HttpException $err) {
                return new ErrResponse($err->getMessage());
        }

        $this->likeRepository->save($like);

        return new SuccessFullResponse([
            'uuid' => (string)$newLikePostUuid
            ]);
    }
}