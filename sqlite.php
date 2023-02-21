<?php

$connectDB = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');


//_______________таблица пользователей

// $connectDB->exec('CREATE TABLE users (
//     uuid TEXT NOT NULL 
//       CONSTRAINT uuid_primary_key PRIMARY KEY,
//     login VARCHAR(150) NOT NULL
//       CONSTRAINT login_unique_key UNIQUE,
//     user_name VARCHAR(100) NOT NULL,
//     user_surname VARCHAR(100) NOT NULL
//    )');


//_______________таблица статей

// $connectDB->exec('CREATE TABLE posts (
//   uuid TEXT NOT NULL 
//     CONSTRAINT uuid_primary_key PRIMARY KEY,
//   author_uuid TEXT NOT NULL,
//   title VARCHAR(100) NOT NULL,
//   content TEXT NOT NULL
// )');


//_______________таблица комментариев

// $connectDB->exec('CREATE TABLE comments (
//     uuid TEXT NOT NULL 
//       CONSTRAINT uuid_primary_key PRIMARY KEY,
//     author_uuid TEXT NOT NULL,
//     article_uuid TEXT NOT NULL,
//     text TEXT NOT NULL
// )');


//_______________таблицы лайков

// $connectDB->exec('CREATE TABLE post_likes (
//     uuid TEXT NOT NULL PRIMARY KEY,
//     author_uuid TEXT NOT NULL,
//     article_uuid TEXT NOT NULL,
//     FOREIGN KEY (author_uuid) REFERENCES users(uuid)
//     FOREIGN KEY (article_uuid) REFERENCES posts(uuid)    
// )');

// $connectDB->exec('CREATE TABLE comment_likes (
//     uuid TEXT NOT NULL PRIMARY KEY,
//     author_uuid TEXT NOT NULL,
//     comment_uuid TEXT NOT NULL,
//     FOREIGN KEY (author_uuid) REFERENCES users(uuid)
//     FOREIGN KEY (comment_uuid) REFERENCES comments(uuid)    
// )');

// __________________________токены


// $connectDB->exec('CREATE TABLE tokens (
//         token TEXT NOT NULL PRIMARY KEY,
//         user_uuid TEXT NOT NULL,
//         expires TEXT NOT NULL
//         )'
//     );
    
//______________________________________________________________________________

// $connectDB->exec(
//     "INSERT INTO users (user_name, user_surname) VALUES ('Nikita', 'Ivanov')"
// );
//

// $connectDB->exec(
//     "DROP TABLE comments"
// );


// $connectDB->exec(
// "ALTER TABLE users ADD password TEXT"
// );