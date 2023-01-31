<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class Post
{
    public function __construct(
        private UUID $uuid, 
        private User $auther,
        private string $title,
        private string $content
    )
    {        
    }

    public function  __toString() 
    {
        return $this->title . "\n" . $this->content . ".\n" . 'Автор: ' . $this->auther  . PHP_EOL;
    }  


    
    public function getUuid (): UUID
    {
        return $this->uuid;
    }
    public function getAuther (): User
    {
        return $this->auther;
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
