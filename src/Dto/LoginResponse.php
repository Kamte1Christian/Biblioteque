<?php

namespace App\Dto;

use App\Entity\User;

class LoginResponse
{
    public function __construct(
        public string $token,
        public User $user
    ) {}
}
