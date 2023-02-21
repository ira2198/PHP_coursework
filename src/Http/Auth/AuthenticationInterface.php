<?php

namespace GeekBrains\LevelTwo\Http\Auth;

use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\User;

interface AuthenticationInterface
{
    public function author(Request $request): User;
}