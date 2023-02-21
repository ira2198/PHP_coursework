<?php

namespace GeekBrains\LevelTwo\Blog\Likes;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class LikePost extends Likes 
{
    private Post $postLike;

    public function __construct(UUID $uuid, User $authorUuid, Post $postLike)
    {
        parent::__construct($uuid, $authorUuid);
        $this->postLike = $postLike;
    }

    public function getPostLike(): Post
    {
        return $this->postLike;   
    }
}