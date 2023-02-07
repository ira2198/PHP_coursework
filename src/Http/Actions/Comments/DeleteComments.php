<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;

class DeleteComments implements ActionsInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {        
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->query('uuid');
            $this->commentsRepository->get(new UUID($commentUuid));
        } catch (CommentNotFoundException $err){
            return new ErrResponse($err->getMessage());
        } 

        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessFullResponse([
            'uuid'=>$commentUuid
        ]);
        
    }

}