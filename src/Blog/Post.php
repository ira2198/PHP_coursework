<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Users\User;

class Post
{
    public function __construct(
        private int $id, 
        private User $userId,
        private string $title,
        private string $content
    )
    {        
    }

    public function  __toString() 
    {
        return $this->title . "\n" . $this->content . ".\n" . 'Автор: ' . $this->userId  . PHP_EOL;
    }  


    
    public function getId (): int
    {
        return $this->id;
    }
    public function getUserId (): User
    {
        return $this->userId;
    }
    public function getTitle (): string
    {
        return $this->title;
    }
    public function getContent (): string
    {
        return $this->content;
    }


    public function setTitle (string $title): void
    {
        $this->title = $title;
    }
    public function setContent ( string $content): void
    {
        $this->content= $content;
    }

}
