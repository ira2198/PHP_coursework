<?php

namespace GeekBrains\UnitTests\BlogTests;

use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Blog\{Post, Comment};
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;
use GeekBrains\LevelTwo\Users\Exceptions\CommentNotFoundException;
use GeekBrains\UnitTests\DummyLogger;
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

        $repository = new SqliteCommentsRepository($connectionMock, new DummyLogger);

        $this->expectExceptionMessage('Cannot find post: d02eef61-1a06-460f-b859-202b84164734');
        $this->expectException(CommentNotFoundException::class);
        $repository->get(new UUID('d02eef61-1a06-460f-b859-202b84164734'));
    }
    
    public function testItSavesCommentsToDatabase(): void
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
        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger);

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
          
    public function testItGetCommentByUuid(): void
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

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger);
        $comment = $commentRepository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$comment->getUuid());
    }
}