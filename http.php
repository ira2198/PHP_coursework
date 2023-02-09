<?php

use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComments;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComments;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByLogin;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Users\Exceptions\AppException;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;

$container = require __DIR__ . '/bootstrap.php';


$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input')
);

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



$routes = [
    'GET'=>[
        '/user/show' => FindByLogin::class
    ],
    'POST' => [
        '/user/create' => CreateUser::class,
        '/post/create' => CreatePost::class,          
        '/comment/create' => CreateComments::class,
    ],
    'DELETE' => [
        '/post/delete' => DeletePost::class,
        '/comment/delete' => DeleteComments::class,
    ]
];

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

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {

$response = $action->handle($request);

} catch (AppException $err) {
    (new ErrResponse($err->getMessage()))->send();
}

$response->send();


