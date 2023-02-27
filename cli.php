<?php

use GeekBrains\LevelTwo\Users\Commands\CommentsCommand\CommentsDelete;
use GeekBrains\LevelTwo\Users\Commands\FakeData\PopulateDB;
use GeekBrains\LevelTwo\Users\Commands\PostsCommands\PostDelete;
use GeekBrains\LevelTwo\Users\Commands\UsersCommands\CreateUser;
use GeekBrains\LevelTwo\Users\Commands\UsersCommands\UpdateUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

// $faker = Faker\Factory::create('ru_RU');

// обьект приложения
$application = new Application(); // класс из симфони

//классы комманд (namespace из command)
$commandsClasses = [
    CreateUser::class,
    PostDelete::class,
    CommentsDelete::class,
    UpdateUser::class,
    PopulateDB::class
];

// пройдемся по этому массиву
foreach ($commandsClasses as $commandClass){
    // +новый обЪект комманды
    $command = $container->get($commandClass);
    
    // добавляем комманду к приложению
    $application->add($command);
}

try {
    // запускаем обработку комманд
   $application->run();
    
} catch (Exception $err) {
    $logger->error($err->getMessage(), ['exeption' => $err]);
    echo $err->getMessage();
}


/// php cli.php users:create --help
/// Пр. php cli.php user:create userSymf Ivan Ivanov 111