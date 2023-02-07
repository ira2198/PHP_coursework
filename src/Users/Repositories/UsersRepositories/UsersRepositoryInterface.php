<?php

namespace GeekBrains\LevelTwo\Users\Repositories\UsersRepositories;

use GeekBrains\LevelTwo\Users\User;
use GeekBrains\LevelTwo\Users\UUID;

interface UsersRepositoryInterface
{
    public function save (User $user): void;
    public function get(UUID $uuid): User;
    public function getByUserLogin(string $login): User;
}