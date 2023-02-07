<?php

namespace tests\ActionTests;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Users\Exceptions\UserNotFoundExceptions;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{
    private function postRepository(): PostRepositoryInterface
    {
        return new class() implements PostRepositoryInterface {
            private bool $called = false;
            
            public function __construct(){
            }
            
            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getCall(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid): void
            {}
        };
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements  UsersRepositoryInterface
        {
            public function __construct( 
                private array $users
            )
            {               
            }

            public function save(User $user):void {}


            public function get(UUID $uuid): User{
                foreach($this->users as $user) {
                    if ($user instanceof User && (string)$uuid == $user->getUuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundExceptions('Cannot find user: ' . $uuid);
            }

            public function getByUserLogin(string $login): User
            {
                throw new UserNotFoundExceptions('Not found');
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(
            [], [], '{"author_uuid":"855ede84-4722-4678-b8bd-4f711ffa4e25",
                "title":"lorem",
                "content":"content lorem"}');


        $postsRepository = $this->postRepository();

        $userRepository = $this->usersRepository([
            new User(
                new UUID('855ede84-4722-4678-b8bd-4f711ffa4e25'),
                'login',
                'user_name',
                'user_surname'),
            ]);

        $action = new CreatePost($postsRepository, $userRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfullResponse::class, $response);

        $this->setOutputCallback(function ($data){
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        $dataDecode['data']['uuid'] = "855ede84-4722-4678-b8bd-4f711ffa4e25";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString(
            '{"success":true,"data":{"uuid":"855ede84-4722-4678-b8bd-4f711ffa4e25"}}'
        );
        $response->send();
    }

     /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrResponseIfNotFoundUser(): void
    {
        $request = new Request(
            [], [], '{"author_uuid":"855ede84-4722-4678-b8bd-4f711ffa4e25",
                "title":"lorem",
                "content":"content lorem"}');
            
        $postsRepository = $this->postRepository();
        $userRepository = $this->usersRepository([]);

        $action = new CreatePost($postsRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(SuccessfullResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Cannot find user: 855ede84-4722-4678-b8bd-4f711ffa4e25"}'
        );
        
        $response->send();
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

     public function testItReturnsErrResponseIfNoContentProvided(): void
     {
        $request = new Request(
            [], [], '{"author_uuid":"855ede84-4722-4678-b8bd-4f711ffa4e25",
                "title":"lorem"}'
            );

        $postsRepository = $this->postRepository([]);
        $userRepository = $this->usersRepository([
            new User(
                new UUID('855ede84-4722-4678-b8bd-4f711ffa4e25'),
                'login',
                'user_name',
                'user_surname'),
            ]);
        $action = new CreatePost($postsRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(SuccessfullResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: content"}');

        $response->send();
     }

}