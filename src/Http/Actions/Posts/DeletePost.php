<?php


namespace GeekBrains\LevelTwo\Http\Actions\Posts;
 
use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;

class DeletePost implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository

    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $err){
            return new ErrResponse($err->getMessage());
        } 

        $this->postRepository->delete(new UUID($postUuid));

        return new SuccessFullResponse([
            'uuid'=>$postUuid
        ]);
        

    }
}
