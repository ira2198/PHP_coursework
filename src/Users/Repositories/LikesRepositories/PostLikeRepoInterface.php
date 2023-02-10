<?php

namespace GeekBrains\LevelTwo\Users\Repositories\LikesRepositories;

use GeekBrains\LevelTwo\Blog\Likes\LikePost;
use GeekBrains\LevelTwo\Users\UUID;

interface PostLikeRepoInterface 
{
    public function save(LikePost $like);
    public function getLikePost(UUID $uuid): LikePost;
    public function checkUserLikeForPostExists($authorUuid, $articleUuid): void;
}