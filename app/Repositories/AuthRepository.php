<?php

namespace App\Repositories;

use App\Classes\ApiCatchErrors;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     *
     * Summary: user  register
     *
     * @param mixed $data
     * @return User
     */
    public function register(array $data): User
    {
        try {

            return User::create($data);

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }

    /**
     *
     * Summary: user  login
     *
     * @param mixed $data
     * @return array
     */
    public function login(array $data): array
    {
        try {
            $success = [];

            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('MyApp')->plainTextToken;
                $success['name'] = $user->name;
                $success['status'] = true;
            } else {
                $success['status'] = false;
            }

            return $success;

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }

    /**
     *
     * Summary: user logout
     *
     * @return bool
     */
    public function logout(): bool
    {
        try {
            Auth::user()->tokens()->delete();

            return true;

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }
}
