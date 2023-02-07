<?php

use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComments;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComments;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByLogin;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\SuccessFullResponse;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Users\Exceptions\AppException;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;



require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'),);

// http://localhost/user/show?login=admin   - Для запроса

// {
//     "author_uuid": "398fcb67-c1aa-496e-9a42-3927aa6788c6",
//     "article_uuid": "ea24d177-bbdb-4c09-b369-522d429c87a9",
//     "text": "попробуем слона"
// }

$routes = [
    'GET'=>[
        '/user/show' => new FindByLogin(
            new SqliteUsersRep(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),                 
    ],
    'POST' => [
        '/user/create' => new CreateUser(
            new SqliteUsersRep(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/post/create' => new CreatePost(
            new sqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),           
            new SqliteUsersRep(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')                
            )
        ),
        '/comment/create' => new CreateComments(
            new sqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new sqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),           
            new SqliteUsersRep(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')                
            )
        )
    ],
    'DELETE' => [
        '/post/delete' => new DeletePost(
            new sqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )   
        ),
        '/comment/delete' => new DeleteComments(
            new sqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )   
        )
    ]
];


try {
    $path = $request->path();
} catch (HttpException) {

    (new ErrResponse())->send();
    return;
}


try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrResponse())->send();
    return;
}


// Есть ли маршруты для методов запроса
if (!array_key_exists($method, $routes)) {
    (new ErrResponse('Not Found'))->send();
    return;
}

// А есть ли этот маршрут в запросе

if (!array_key_exists($path, $routes[$method])) {
    (new ErrResponse('Not Found'))->send();
    return;
}

$action = $routes[$method][$path];


try {

$response = $action->handle($request);

} catch (AppException $err) {
    (new ErrResponse($err->getMessage()))->send();
}

$response->send();

//$path = $request->path();

// $parameter = $request->query('some_parameter');
// $header = $request->header('Some-Header');
// $path = $request->path();


// $response = new SuccessFullResponse([
//     'messege' => 'Hello from PHP',
// ]);

// $response->send();


//$name = $request->header('cookie');


// http_response_code(201);  // установить другой код ответа

// // Устанавливаем заголовки
// header('Some-Header: some_value');
// header('One-More-Header: another_value');

// // GET http://127.0.0.1:8000
// //Cookie: XDEBUG_SESSION=start // для отладки в запрос

// // $a = 5+6; 
// // $a ++;
// // echo "hello";
