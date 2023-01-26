<?php
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Blog\{Post, Comment};

 require __DIR__ . "/vendor/autoload.php";

 $faker = Faker\Factory::create('ru_RU');

// spl_autoload_register('customAutoloader');


// function customAutoloader($class){
//     $file = $class . ".php";
//     $file = str_replace("\\", "/", $file);
//     $file = str_replace("GeekBrains/LevelTwo/", "src/", $file);
//     var_dump($file);
//     if (file_exists($file)){
//          include $file;
//     }
// }

$person1 = new User(
    $faker->randomDigitNotNull(),
    $faker->firstName(),
    $faker->lastName());

$post1 = new Post(
    $faker->randomDigitNotNull(),
    $person1,
    $faker->realText(rand(5, 30)), 
    $faker->realText(rand (200, 400))
);

$comment1 = new Comment(
    $faker->randomDigitNotNull(),
    $person1,
    $post1, 
    $faker->realText(rand (20, 100))
);


$marker = $argv[1] ?? null;

switch ($marker){
    case "user":
        echo $person1; 
        break;
    case "post":
        echo $post1;
        break;
    case "comment":
        echo $comment1;
        break;
    case "all":
        echo $person1; 
        echo $post1;
        echo $comment1;
        break;
        case null:
          echo  "Ошибка! Вы не указали маркер или указали его не верно.";
}


