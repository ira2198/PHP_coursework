<?php

namespace GeekBrains\LevelTwo\Users\Repositories\LikesRepositories;

use GeekBrains\LevelTwo\Blog\Likes\Likes;
use GeekBrains\LevelTwo\Blog\Likes\LikePost;
use GeekBrains\LevelTwo\Users\Exceptions\LikeAlreadyExists;
use GeekBrains\LevelTwo\Users\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Users\UUID;
use PDO;

class SqlitePostLikeRepo implements PostLikeRepoInterface
{
    public function __construct(
        private PDO $connectDB
    ) 
    {        
    }   

    public function save(LikePost $like)
    {
        $statement = $this->connectDB->prepare(
            "INSERT INTO post_likes (uuid, author_uuid, article_uuid) VALUES (:uuid, :author_uuid, :article_uuid)");
            
            $statement->execute([
                ':uuid' => $like->getUuid(),
                ':author_uuid' => $like-> getAuthorUuid()->getUuid(),
                ':article_uuid' => $like-> getPostLike()->getUuid()
                ,
            ]);
    }

    public function getLikePost(UUID $uuid): LikePost
    {
        $statement = $this-> connectDB-> prepare (
            'SELECT * FROM post_likes WHERE uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);
        return $this->getLike($statement, $uuid);
    }

    
    public function getLike(\PDOStatement $statement, string $uuid): LikePost
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new LikeNotFoundException(  
                "Cannot find like: $uuid");
        }

        return new LikePost(                            
            new UUID($result['uuid']),
            $result['author_uuid'],
            $result['article_uuid'],
            );
    }
    
    public function checkUserLikeForPostExists($authorUuid, $articleUuid): void
    {
        $statement = $this->connectDB->prepare(
            'SELECT * FROM post_likes 
            WHERE author_uuid = :author_uuid 
            AND article_uuid = :article_uuid'
        );

        $statement->execute([
            ':author_uuid' => $authorUuid,
            ':article_uuid' => $articleUuid
        ]);
        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this text already exists'
            );
        }

    }


   
}