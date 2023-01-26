<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Users\User;


class Comment
{
    public function __construct(
        private int $id,
        private User $userId,
        private Post $articleId,
        private string $text
    ){        
    }

    public function  __toString() 
    {
        return 'Пользователь: ' . $this->userId . "Оставил комментарий к статье \n" . $this->text  . PHP_EOL;
    }  


    
    public function getId (): int
    {
        return $this->id;
    }
    public function getUserId (): User
    {
        return $this->userId;
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
