<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Support\Facades\Lang;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if($token = Auth::attempt(['user' => $request['user'], 'password' => $request['password']])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('tmhLogistc'); 
            return response()->json(['success' => $success], 200); 
        } 
        else{ 
            return response()->json(['error'=>'Usuario o contraseña incorrecta'], 401); 
        } 
    }
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'user' => 'required', 
            'email' => 'required|email', 
            'password' => 'required',
            'type_admin' => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        return response()->json(["msg" => "Se ha creado el usuario de forma exitosa"], 200); 
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'token' => $user->createToken('tmhLogistc')->accessToken
        ]);
    }
}
