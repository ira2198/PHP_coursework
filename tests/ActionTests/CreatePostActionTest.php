<?php

namespace GeekBrains\UnitTests\ActionTests;


use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Auth\JsonBodyUuidIdentification;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Users\Exceptions\AuthExceptions;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;
use GeekBrains\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{

   
    public function testItReturnsSuccessAnswer(): void
    {
        $postRepositoryStub = $this->createStub(PostRepositoryInterface::class);
        $identificationStub = $this->createStub(JsonBodyUuidIdentification::class);

        $identificationStub->method('author')->willReturn(
            new User(
                new UUID('855ede84-4722-4678-b8bd-4f711ffa4e25'),
                'login',
                'user_name',
                'user_surname'),
            );

        $createPost = new CreatePost(
            $postRepositoryStub, $identificationStub, new DummyLogger(),
        );

        $request = new Request([], [],'{"title": "title", "content": "content"}');
      

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            SuccessFullResponse::class,
            $actual
        );
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
   
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(
            [], [], '{"auther_uuid":"855ede84-4722-4678-b8bd-4f711ffa4e25",
                "title":"title", 
                "content":"content"}');

        $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);
        $identificationStub = $this->createStub(JsonBodyUuidIdentification::class);

        $identificationStub->method('author')->willReturn(
            new User(
                new UUID('855ede84-4722-4678-b8bd-4f711ffa4e25'),
                'login',
                'user_name',
                'user_surname'),
        );            

        $action = new CreatePost($postsRepositoryStub, $identificationStub, new DummyLogger);

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
                "title":"title","content":""content}'
            );
            
        $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);
        $identificationStub = $this->createStub(JsonBodyUuidIdentification::class);


        $identificationStub->method('author')->willThrowException(
            new AuthExceptions("Cannot find user: 855ede84-4722-4678-b8bd-4f711ffa4e25")
        );

        $action = new CreatePost($postsRepositoryStub, $identificationStub, new DummyLogger);

        $response = $action->handle($request);
        $response->send();

        $this->assertInstanceOf(ErrResponse::class, $response);
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
                "title":"title"}'
            );

        $identificationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);

        $identificationStub->method('author')->willReturn( 
            new User(
                new UUID('855ede84-4722-4678-b8bd-4f711ffa4e25'),
                'login',
                'user_name',
                'user_surname'),
            );
        $action = new CreatePost($postsRepositoryStub, $identificationStub, new DummyLogger);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: content"}');

        $response->send();
     }

}