<?php


namespace GeekBrains\LevelTwo\Http\Actions\Posts;
 
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private TokenAuthIdenticationInterface $tokenAuth,
        private LoggerInterface $logger
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $this->tokenAuth->author($request);
        } catch (AuthExceptions $err) {
            return new ErrResponse($err->getMessage());
        }

        try {
            $postUuid = $request->query('uuid');
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $err){
            return new ErrResponse($err->getMessage());
        } 

        $this->postRepository->delete(new UUID($postUuid));

        $this->logger->info("Post: $postUuid was successfully deleted in DeletePost");

        return new SuccessFullResponse([
            'uuid'=>$postUuid
        ]);
        

    }
}
