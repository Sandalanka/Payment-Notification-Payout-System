<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    /**
     * Summary: user register interface method
     * 
     * @param  mixed $data
     * @return void
     */
    public function register(array $data);

    /**
     * Summary: user  login interface method
     *
     * @param  mixed $data
     * @return void
     */
    public function login(array $data);

    /**
     * Summary: user  logout interface method
     * 
     *
     * @return void
     */
    public function logout();
}
