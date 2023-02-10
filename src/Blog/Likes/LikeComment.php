<?php

namespace GeekBrains\LevelTwo\Blog\Likes;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

class LikeComment extends Likes
{
    private Comment $commentLike;

    public function __construct(UUID $uuid, User $authorUuid, Comment $commentLike)
    {
        parent::__construct($uuid, $authorUuid);
        $this->$commentLike = $commentLike;
    }

    public function getCommentLike(): Comment
    {
        return $this->commentLike;   
    }
}
