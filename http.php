<?php

use GeekBrains\LevelTwo\Http\Actions\AuthAction\Login;
use GeekBrains\LevelTwo\Http\Actions\AuthAction\LogOut;
use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComments;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComments;
use GeekBrains\LevelTwo\Http\Actions\likes\CreateLikeComment;
use GeekBrains\LevelTwo\Http\Actions\likes\CreateLikePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByLogin;
use GeekBrains\LevelTwo\Http\ErrResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Users\Exceptions\AppException;
use GeekBrains\LevelTwo\Users\Exceptions\HttpException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);


$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input')
);


try {
    $path = $request->path();
} catch (HttpException $err) {
    $logger->warning($err->getMessage());
    (new ErrResponse())->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $err) {
    $logger->warning($err->getMessage());
    (new ErrResponse())->send();
    return;
}


$routes = [
    'GET'=>[
        '/user/show' => FindByLogin::class
    ],
    'POST' => [
        '/logout'=> LogOut::class,
        '/login' => Login::class,  
        '/user/create' => CreateUser::class,
        '/post/create' => CreatePost::class,          
        '/comment/create' => CreateComments::class,
        '/post-like/create' => CreateLikePost::class,
        '/comment-like/create' => CreateLikeComment::class
    ],
    'DELETE' => [
        '/post/delete' => DeletePost::class,
        '/comment/delete' => DeleteComments::class,
    ]
];

// Есть ли маршруты для методов запроса или есть ли маршрут в запросе
if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {

    $logger->notice('Not Found');
   (new ErrResponse('Not Found'))->send();
    return;
}


$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {

$response = $action->handle($request);

} catch (AppException $err) {
    
    $logger->error($err->getMessage(), ['exeption' => $err]);
    (new ErrResponse($err->getMessage()))->send();
    return;
}

$response->send();
