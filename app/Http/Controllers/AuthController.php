<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\DB; 
use App\Classes\ApiCatchErrors;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\BaseController as BaseController;

class AuthController extends BaseController
{
    private AuthRepositoryInterface $authRepositoryInterface;
    
    /**
     * __construct
     *
     * @param  mixed $authRepositoryInterface
     * @return void
     */
    public function __construct(AuthRepositoryInterface $authRepositoryInterface)
    {
        $this->authRepositoryInterface = $authRepositoryInterface;
    }
    
    /**
     * Summary: user register function
     *
     * @param  mixed $request
     * @return void
     */
    public function register(RegisterRequest $request){
        DB::beginTransaction();
        $userDetails =[
            'name' =>$request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ];
        try{
            $user =$this->authRepositoryInterface->register($userDetails);
            DB::commit();
            return $this->sendResponse(new UserResource($user),'User Register Successful', 201);

        }catch(\Exception $exception){
            return ApiCatchErrors::rollback($exception);
        }
    }
        
    /**
     * Summary: user login function
     *
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request){
        $userDetails =[
            'email' => $request->email,
            'password' =>$request->password
        ];
        try{
            $login =$this->authRepositoryInterface->login($userDetails);
            if($login['status']){
                return $this->sendResponse($login,"Login Successful",200);
            }else{
                return $this->sendResponse("","Login Crediantial incorrect",200);
            }

        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
        }
    }
    
    /**
     * Summary: user logout function
     *
     * @return void
     */
    public function logout(){
        $this->authRepositoryInterface->logout();
        return $this->sendResponse("","logout Successful",200);
    }
}
