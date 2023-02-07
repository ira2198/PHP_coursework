<?php

namespace GeekBrains\LevelTwo\Users\Repositories\PostsRepositories;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Users\UUID;

interface PostRepositoryInterface 
{
    public function save(Post $post);
    public function get(UUID $uuid);
    public function delete(UUID $uuid);
}