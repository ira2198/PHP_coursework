<?php

namespace GeekBrains\LevelTwo\Users\Repositories\PostsRepositories;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use PDO;



class sqlitePostsRepository implements PostRepositoryInterface 
{


    public function __construct(
        private PDO $connectDB
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
        }

        $userRepository = new SqliteUsersRep($this->connectDB);
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
        $statement= $this->connectDB->prepare(
            'DELETE FROM posts WHERE posts.uuid = :uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

    }

}