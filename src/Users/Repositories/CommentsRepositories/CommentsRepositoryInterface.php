<?php

namespace GeekBrains\LevelTwo\Users\Repositories\CommentsRepositories;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Users\UUID;


interface CommentsRepositoryInterface 
{
    public function save(Comment $comment);
    public function get(UUID $uuid);
    public function delete(UUID $uuid);
}