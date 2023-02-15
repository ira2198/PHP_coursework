<?php


use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Users\Commands\Arguments;
use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Blog\{Post, Comment};
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$faker = Faker\Factory::create('ru_RU');

try {
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));
    
} catch (Exception $err) {
    $logger->error($err->getMessage(), ['exeption' => $err]);
    echo $err->getMessage();
}