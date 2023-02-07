<?php

namespace GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories;

use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;

use PDO;
class sqliteCommentsRepository implements CommentsRepositoryInterface 
{
    
    public function __construct(
        private PDO $connectDB
    )
    {        
    }

    public function save(Comment $comment)
    {
        $statement = $this->connectDB->prepare(
            "INSERT INTO comments (uuid, author_uuid, article_uuid, text) VALUES (:uuid, :author_uuid, :article_uuid, :text)");
            
            $statement->execute([
                ':uuid' => $comment->getUuid(),
                ':author_uuid' => $comment->getUserId()->getUuid(),
                ':article_uuid' => $comment -> getArticleId ()->getUuid(),
                ':text' => $comment-> getText()
             ]);
    }


    public function get(UUID $uuid)
    {
        $statement = $this-> connectDB-> prepare (
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement-> execute([':uuid' => (string)$uuid]);

       print_r($statement);
        return $this->getComment($statement, $uuid);
       
    }

    private function getComment(\PDOStatement $statement, string $commentUuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(  
                "Cannot find post: $commentUuid");
        }

             
        $userRepository = new SqliteUsersRep($this->connectDB);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        $postRepository = new sqlitePostsRepository($this->connectDB);
        $post = $postRepository->get(new UUID($result['article_uuid']));

            
        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }  
    public function delete(UUID $uuid)
    {
        $statement= $this->connectDB->prepare(
            'DELETE FROM comments WHERE comments.uuid = :uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

    }


}
