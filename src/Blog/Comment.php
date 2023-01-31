<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private User $auther,
        private Post $articleId,
        private string $text
    ){        
    }

    public function  __toString() 
    {
        return 'Пользователь: ' . $this->auther . "Оставил комментарий к статье \n" . $this->text  . PHP_EOL;
    }  


    
    public function getUuid (): UUID
    {
        return $this->uuid;
    }
    public function getUserId (): User
    {
        return $this->auther;
    }
    public function getArticleId (): Post
    {
        return $this->articleId;
    }
    public function getText (): string
    {
        return $this->text;
    }


   
    public function setText ( string $text): void
    {
        $this->text= $text;
    }

}
