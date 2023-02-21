<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Http\Actions\ActionsInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\UUID;
use Psr\Log\LoggerInterface;

class DeleteComments implements ActionsInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
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
            $commentUuid = $request->query('uuid');
            $this->commentsRepository->get(new UUID($commentUuid));
        } catch (CommentNotFoundException $err){
            return new ErrResponse($err->getMessage());
        } 

        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessFullResponse([
            'uuid'=>$commentUuid
        ]);
        $this->logger->info("comment: $commentUuid deleted successfully in CreateComments class");
    }

}