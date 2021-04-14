<?php

namespace App\Http\Controllers;

use http\Env\Response;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required|string|between:2,100',
            'email' => 'nullable|required|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json
            ($validator->errors(),400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ],201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if(!$token = auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function userProfile(){
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
