<?php


use GeekBrains\LevelTwo\Container\DIContainer;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\CommentLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\PostLikeRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\SqliteLikeCommentsRep;
use GeekBrains\LevelTwo\Users\Repositories\LikesRepositories\SqlitePostLikeRepo;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\PostRepositoryInterface;
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;
use GeekBrains\LevelTwo\Http\Auth\AuthenticationInterface;
use GeekBrains\LevelTwo\Http\Auth\BearerTokenAuthentication;
use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Http\Auth\JsonBodyUuidIdentification;
use GeekBrains\LevelTwo\Http\Auth\JsonByLoginIdentification;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthentication;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthIdenticationInterface;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\AuthTokenRepoInterface;
use GeekBrains\LevelTwo\Users\Repositories\TokenRepository\sqliteAuthTokenRepository;

require_once __DIR__ . '/vendor/autoload.php';

// для чтения .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();


// 1. подключаем его к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH']) // читаем базу через env
    // new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

//Сoздаем и настраиваем логгер

$logger = (new Logger('blog')); //blog - произвольное имя логгера

if ('yes' === $_ENV['LOG_TO_FILES']) {
    //записи сохраняем в файл
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.log'))
    // Расширим
    ->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.error.log',
        level: Logger::ERROR, //error и выше
        bubble: false // cобытие не будет всплывать
));
}

if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger->pushHandler(new StreamHandler("php://stdout")) ; // сыпем в консоль
}


$container->bind(
    LoggerInterface::class,
    $logger
);
// 2. репозиторий статей
$container->bind(
    PostRepositoryInterface::class,
    sqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRep::class
);
// 4. репозиторий комментариев
$container->bind(
    CommentsRepositoryInterface::class,
    sqliteCommentsRepository::class
);

// 5. репозиторий лайков
$container->bind(
    PostLikeRepoInterface::class,
    SqlitePostLikeRepo::class
);
$container->bind(
    CommentLikeRepoInterface::class,
    SqliteLikeCommentsRep::class
);

// Идентификация, аутентификация
$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    IdentificationInterface::class,
    JsonByLoginIdentification::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
); 
$container->bind(
    AuthTokenRepoInterface::class,
    sqliteAuthTokenRepository::class
);

$container->bind(
    TokenAuthIdenticationInterface::class,
    BearerTokenAuthentication::class
);



return $container;

