<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     *
     * Summary: user register interface method
     *
     * @param mixed $data
     * @return User
     */
    public function register(array $data): User;

    /**
     *
     * Summary: user  login interface method
     *
     * @param mixed $data
     * @return array
     */
    public function login(array $data): array;

    /**
     *
     * Summary: user  logout interface method
     *
     * @return bool
     */
    public function logout(): bool;
}
