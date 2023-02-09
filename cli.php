<?php


use GeekBrains\LevelTwo\Users\Repositories\UsersRepositories\SqliteUsersRep;
use GeekBrains\LevelTwo\Users\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Users\{User, UUID};
use GeekBrains\LevelTwo\Blog\{Post, Comment};
use GeekBrains\LevelTwo\Users\Repositories\PostsRepositories\sqlitePostsRepository;
use GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories\sqliteCommentsRepository;


$container = require __DIR__ . '/bootstrap.php';



$faker = Faker\Factory::create('ru_RU');

$userRepository = new SqliteUsersRep($connectDB);
$postRepository = new sqlitePostsRepository($connectDB);
$commentsRepository = new sqliteCommentsRepository($connectDB);



//________________________Создаем Юзеров___________________

// $userRepositiry->save(new User(UUID::random(), "VoVo", "Vova", "Volodin"));
// $userRepositiry->save(new User(UUID::random(), "admin", "Ola", "Lola"));


// $command = new CreateUserCommand($userRepositiry);
// try {
//     $command->handle($argv);
// } catch (Exception $err) {
//     echo $err->getMessage();
// } 

//__________ И извлекаем______________

try {
    echo $userRepository->getByUserLogin('VoVo');
} catch (Exception $err) {
    echo $err->getMessage();
} 



//_______________Создаем и извлекаем посты________

// try {
//     $user= $userRepositiry-> get(new UUID('49a45dd2-37cc-44b2-9c90-0212e15ba067'));

//     $post = new Post (
//         UUID::random(),
//         $user,
//         $faker->realText(rand(5, 30)),
//         $faker->realText(rand (100, 400))
//     );
//     $postRepositiry->save($post);

// } catch (Exception $err) {
//     echo $err->getMessage();
// }

// try {
//     $post = $postRepositiry->get(new UUID('f8541067-f5c8-450b-a696-f037261277fb'));

//     print_r($post);

// } catch (Exception $err) {
//     echo $err->getMessage();
// }

//_______________Создаем и извлекаем коммертарии________

// try {
//     $post= $postRepository-> get(new UUID('f8541067-f5c8-450b-a696-f037261277fb'));
//     $user= $userRepository-> get(new UUID('1e7894ab-b949-4f9f-b855-d38020fe7bd6'));

//     $comment = new Comment (
//         UUID::random(),
//         $user,
//         $post,
//         $faker->realText(rand (50, 100))
//     );

//     print_r($comment);
//     $commentRepository->save($comment);

// } catch (Exception $err) {
//     echo $err->getMessage();
// }

