<?php

namespace GeekBrains\LevelTwo\Http\Auth;

use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Users\User;

interface IdentificationInterface 
{
    public function author(Request $request): User;
}