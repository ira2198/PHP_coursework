<?php

namespace GeekBrains\LevelTwo\Users\Repositories\PostsRepositories;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\levelTwo\Users\Exceptions\PostRepoExeption;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class sqlitePostsRepository implements PostRepositoryInterface 
{


    public function __construct(
        private PDO $connectDB,
        private LoggerInterface $logger
    ) 
    {        
    }   

    public function save(Post $post)
    {
        $statement = $this->connectDB->prepare(
            "INSERT INTO posts (uuid, author_uuid, title, content) VALUES (:uuid, :author_uuid, :title, :content)");
            
            $statement->execute([
                ':uuid' => $post->getUuid(),
                ':author_uuid' => $post-> getAuther()->getUuid(),
                ':title' => $post -> getTitle (),
                ':content' => $post-> getContent()
             ]);

            $this->logger->info("The post: $post was created in sqlitePostsRepository class");
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this-> connectDB-> prepare (
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getPost($statement, $uuid);
    }
   


    private function getPost(\PDOStatement $statement, string $postUuid): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(  
                "Cannot find post: $postUuid");

                $this->logger->warning("Cannot find post: $postUuid in sqlitePostsRepository class");
        }

        $userRepository = new SqliteUsersRep($this->connectDB, $this->logger);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(                            
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['content']
        );
    }

    public function delete(UUID $uuid)
    {
        try {
            $statement= $this->connectDB->prepare(
                'DELETE FROM posts WHERE posts.uuid = :uuid;'
            );

            $statement->execute([
                ':uuid' => $uuid
            ]);
        } catch (PDOException $err) {
            throw new PostRepoExeption(
            $err->getMessage(), (int)$err->getCode(), $err
            );
        }
        $this->logger->info("The post: $uuid was deleted in sqlitePostsRepository class"); 
    }
}