<?php

namespace BlogTests;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Blog\{Post, Comment};
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;
use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find post: d02eef61-1a06-460f-b859-202b84164734');
        $this->expectException(CommentNotFoundException::class);
        $repository->get(new UUID('d02eef61-1a06-460f-b859-202b84164734'));
    }
    
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock 
            ->expects($this->once()) 
            ->method('execute') 
            ->with ([
                ':uuid' =>'123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':article_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':text' => 'comment line'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepository($connectionStub);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'login',
                'user_name',
                'user_surname'
        );

        $post = new Post(            
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Ivan',
                'Nikitin'
        );

        $repository->save(
            new Comment(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                $post,
                'comment line'
            ));
    }
          
    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'article_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'content' => 'Какой-то текст',
            'login' => 'ivan123',
            'user_name' => 'Ivan',
            'user_surname' => 'Nikitin',
            'text' => 'comment line'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub);
        $comment = $commentRepository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$comment->getUuid());
    }







    // public function testItGetPostByUuid(): void
    // {
    //     $connectionStub = $this->createStub(\PDO::class);
    //     $statementStubUser = $this->createMock(\PDOStatement::class);
    //     $statementStubPost = $this->createMock(\PDOStatement::class);
    //     $statementStubComment = $this->createMock(\PDOStatement::class);


    //     $statementStubUser->method('fetch')->willReturn([
    //         'uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
    //         'login' => 'ivan123',
    //         'user_name' => 'Ivan',
    //         'user_surname' => 'Nikitin'
    //     ]);

    //     $statementStubPost->method('fetch')->willReturn ([
    //         'uuid' => '123e4567-e89b-12d3-a456-426614174000',
    //         'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
    //         'title' => 'Заголовок',
    //         'content' => 'Какой-то текст'

    //     ]);

    //     $statementStubComment->method('fetch')->willReturn ([
    //         'uuid' =>'49a45dd2-37cc-44b2-9c90-0212e15ba067',
    //         'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
    //         'article_uuid' => '123e4567-e89b-12d3-a456-426614174000',
    //         'text' => 'comment line'
    //     ]);


    //     $connectionStub->method('prepare')->willReturn($statementStubUser, $statementStubPost, $statementStubComment);
        
    //     $commentRepository = new SqliteCommentsRepository($connectionStub);
    //     $comment = $commentRepository->get(new UUID('49a45dd2-37cc-44b2-9c90-0212e15ba067'));

    //     $this->assertSame('49a45dd2-37cc-44b2-9c90-0212e15ba067', (string)$comment->getUuid());
    // }

}