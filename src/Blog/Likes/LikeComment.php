<?php

namespace GeekBrains\LevelTwo\Blog\Likes;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class LikeComment extends Likes
{   

    public function __construct(UUID $uuid, User $authorUuid, private Comment $commentLike)
    {
        parent::__construct($uuid, $authorUuid);
        
    }

    public function getCommentLike(): Comment
    {
        return $this->commentLike;   
    }
}
