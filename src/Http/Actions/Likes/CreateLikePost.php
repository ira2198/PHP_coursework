<?php

namespace GeekBrains\LevelTwo\Http\Actions\likes;

use GeekBrains\LevelTwo\Blog\Likes\LikePost;
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
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\PostLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class CreateLikePost implements ActionsInterface 
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private PostLikeRepoInterface $likeRepository,
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

        try {
            $articleUuid = new UUID($request->jsonBodyField('article_uuid'));
        } catch (HttpException | InvalidArgumentException $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $this->likeRepository->checkUserLikeForPostExists($user->getUuid(), $articleUuid);
        } catch (LikeAlreadyExists $err) {
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

        $this->logger->info("Like the article: $newLikePostUuid");

        return new SuccessFullResponse([
            'uuid' => (string)$newLikePostUuid
            ]);
    }
}