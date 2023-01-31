<?php

$connectDB = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');


// $connectDB->exec('CREATE TABLE users (
//     uuid TEXT NOT NULL 
//       CONSTRAINT uuid_primary_key PRIMARY KEY,
//     login VARCHAR(150) NOT NULL
//       CONSTRAINT login_unique_key UNIQUE,
//     user_name VARCHAR(100) NOT NULL,
//     user_surname VARCHAR(100) NOT NULL
//    )');



// $connectDB->exec('CREATE TABLE posts (
//   uuid TEXT NOT NULL 
//     CONSTRAINT uuid_primary_key PRIMARY KEY,
//   author_uuid TEXT NOT NULL,
//   title VARCHAR(100) NOT NULL,
//   content TEXT NOT NULL
// )');

// $connectDB->exec('CREATE TABLE comments (
//     uuid TEXT NOT NULL 
//       CONSTRAINT uuid_primary_key PRIMARY KEY,
//     author_uuid TEXT NOT NULL,
//     article_uuid TEXT NOT NULL,
//     text TEXT NOT NULL
// )');

// $connectDB->exec(
//     "INSERT INTO users (user_name, user_surname) VALUES ('Nikita', 'Ivanov')"
// );
//

// $connectDB->exec(
//     "DROP TABLE comments "
// );

