<?php

namespace GeekBrains\LevelTwo\Users\Repositories\LikesRepositories;

use GeekBrains\LevelTwo\Blog\Likes\LikeComment;
use GeekBrains\LevelTwo\Users\UUID;

interface CommentLikeRepoInterface 
{

    public function save(LikeComment $like);
    public function getLikeComment(UUID $uuid): LikeComment;
    public function checkUserLikeForCommentExists($authorUuid, $commentUuid): void;

}