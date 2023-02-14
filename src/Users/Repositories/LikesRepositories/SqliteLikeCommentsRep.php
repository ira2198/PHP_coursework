<?php

namespace GeekBrains\LevelTwo\Users\Repositories\LikesRepositories;

use GeekBrains\LevelTwo\Blog\Likes\LikeComment;
use GeekBrains\LevelTwo\Users\Exceptions\LikeAlreadyExists;
use GeekBrains\LevelTwo\Users\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Users\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqliteLikeCommentsRep implements CommentLikeRepoInterface
{
    public function __construct(
        private PDO $connectDB,
        private LoggerInterface $logger
    ) 
    {        
    }  

    public function save(LikeComment $like)
    {
        $statement = $this->connectDB->prepare(
            "INSERT INTO comment_likes (uuid, author_uuid, comment_uuid) VALUES (:uuid, :author_uuid, :comment_uuid)");
            
            $statement->execute([
                ':uuid' => $like->getUuid(),
                ':author_uuid' => $like-> getAuthorUuid()->getUuid(),
                ':comment_uuid' => $like-> getCommentLike()->getUuid()
                ,
            ]);
            $this->logger->info("like to comment added");
    }

    public function getLikeComment(UUID $uuid): LikeComment
    {
        $statement = $this-> connectDB-> prepare (
            'SELECT * FROM comment_likes WHERE uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);
        return $this->getLike($statement, $uuid);
    }

    public function getLike(\PDOStatement $statement, string $uuid): LikeComment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new LikeNotFoundException(  
                "Cannot find like: $uuid");
        }

        return new LikeComment(                            
            new UUID($result['uuid']),
            $result['author_uuid'],
            $result['comment_uuid'],
            );
    }

    public function checkUserLikeForCommentExists($authorUuid, $commentUuid): void
    {
        $statement = $this->connectDB->prepare(
            'SELECT * FROM comment_likes 
            WHERE author_uuid = :author_uuid 
            AND comment_uuid = :comment_uuid'
        );

        $statement->execute([
            ':author_uuid' => $authorUuid,
            ':comment_uuid' => $commentUuid
        ]);
        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this text already exists'
            );
        $this->logger->warning("The users like for this text already exists");
        }
    }   
}
