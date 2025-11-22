<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Classes\ApiCatchErrors;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepositoryInterface;

    /**
     * __construct
     *
     * @param mixed $authRepositoryInterface
     * @return void
     */
    public function __construct(AuthRepositoryInterface $authRepositoryInterface)
    {
        $this->authRepositoryInterface = $authRepositoryInterface;
    }

    /**
     * Summary: user register function
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userDetails = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ];

            $user = $this->authRepositoryInterface->register($userDetails);

            DB::commit();

            return $this->sendResponse(result: new UserResource($user),
                message: 'User Register Successful',
                statusCode: 201
            );

        } catch (Exception $exception) {
            ApiCatchErrors::rollback($exception);
        }
    }

    /**
     * Summary: user login function
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $userDetails = [
                'email' => $request->email,
                'password' => $request->password
            ];

            $login = $this->authRepositoryInterface->login($userDetails);

            if ($login['status']) {
                return $this->sendResponse(result: $login,
                    message: "Login Successful");
            } else {

                return $this->sendResponse(message: "Login Credential incorrect");
            }

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }

    /**
     * Summary: user logout function
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authRepositoryInterface->logout();

            return $this->sendResponse(message: "logout Successful");

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }

    }
}
