<?php

namespace GeekBrains\LevelTwo\Blog\Likes;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class Likes 
{
    public function __construct(
        private UUID $uuid,
        private User $authorUuid
    )
    {        
    }

    public function getUuid (): UUID
    {
        return $this->uuid;
    }
    public function getAuthorUuid (): User
    {
        return $this->authorUuid;
    }
    

    public function setUuid ($uuid): void
    {
        $this->uuid = $uuid;
    }
    public function setAuthorUuid ($authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

    

}