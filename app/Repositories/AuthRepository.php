<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Summary: user  register
     * 
     *
     * @param  mixed $data
     * @return void
     */
    public function register(array $data){
        return User::create($data);
    }
    
    /**
     * Summary: user  login
     *
     * @param  mixed $data
     * @return void
     */
    public function login(array $data){
        $success =[];
        if(Auth::attempt(['email'=> $data['email'], 'password'=>$data['password']])){
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
            $success['status']=true;
        }else{
            $success['status']=false;
        }
        return $success;
    }
        
    /**
     * Summary: user logout
     *
     * @return void
     */
    public function logout(){
        Auth::user()->tokens()->delete();
        return true;   
    }
}
